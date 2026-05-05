<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\RegistryEntry;
use App\Models\Account;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistryController extends Controller
{
    /**
     * Display a listing of registry entries.
     */
    public function index(Request $request)
    {
        $query = RegistryEntry::with(['currency', 'debtorAccount', 'creditorAccount', 'user']);

        if ($currencyId = $request->input('currency_id')) {
            $query->where('currency_id', $currencyId);
        }

        if ($from = $request->input('from_date')) {
            $query->whereDate('entry_date', '>=', $from);
        }

        if ($to = $request->input('to_date')) {
            $query->whereDate('entry_date', '<=', $to);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('sender', 'LIKE', "%{$search}%")
                  ->orWhere('receiver', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%")
                  ->orWhereHas('debtorAccount', function ($sub) use ($search) {
                      $sub->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('code', 'LIKE', "{$search}%");
                  })
                  ->orWhereHas('creditorAccount', function ($sub) use ($search) {
                      $sub->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('code', 'LIKE', "{$search}%");
                  });
            });
        }

        return response()->json(
            $query->orderByDesc('entry_date')->orderByDesc('id')
                  ->paginate($request->input('per_page', 50))
        );
    }

    /**
     * Store a newly created registry entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric',
            'debtor_account_id' => 'nullable|exists:accounts,id',
            'creditor_account_id' => 'nullable|exists:accounts,id',
            'commission_1' => 'nullable|numeric',
            'commission_2' => 'nullable|numeric',
            'sender' => 'nullable|string|max:255',
            'receiver' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Ensure commissions are never null for database integrity
        $validated['commission_1'] = $validated['commission_1'] ?? 0;
        $validated['commission_2'] = $validated['commission_2'] ?? 0;

        return DB::transaction(function () use ($request, $validated) {
            $validated['user_id'] = $request->user()->id;
            $entry = RegistryEntry::create($validated);

            // 1. Debtor Leg (Customer pays principal + Commission 1)
            if ($entry->debtor_account_id) {
                $totalToCollect = (float)$entry->amount + (float)$entry->commission_1;
                JournalService::record($entry, $entry->debtor_account_id, $entry->currency_id, $totalToCollect, 0, "پسوڵەی #{$entry->id} - مەدین (ئەسڵ + عمولەی ١)", $entry->entry_date);
            }

            // 2. Creditor Leg (Agent gets principal + Commission 2)
            if ($entry->creditor_account_id) {
                $totalToAgent = (float)$entry->amount + (float)$entry->commission_2;
                JournalService::record($entry, $entry->creditor_account_id, $entry->currency_id, 0, $totalToAgent, "پسوڵەی #{$entry->id} - داین (ئەسڵ + عمولەی ٢)", $entry->entry_date);
            }

            // 3. Net Commission Income (Commission 1 - Commission 2 -> Goes to Commission Income in the current branch)
            $netCommission = (float)$entry->commission_1 - (float)$entry->commission_2;
            if ($netCommission != 0) {
                // Find income account in the current branch
                $incomeAccount = Account::withoutGlobalScopes()
                    ->where('branch_id', $entry->branch_id)
                    ->where(function($q) {
                        $q->where('code', '401')
                          ->orWhere('code', 'T03')
                          ->orWhere('type', 'revenue');
                    })
                    ->first();

                if ($incomeAccount) {
                    $debit = $netCommission < 0 ? abs($netCommission) : 0;
                    $credit = $netCommission > 0 ? $netCommission : 0;
                    JournalService::record($entry, $incomeAccount->id, $entry->currency_id, $debit, $credit, "قازانجی سافی حەواڵە - پسوڵەی #{$entry->id}", $entry->entry_date);
                }
            }

            return response()->json(
                $entry->load(['currency', 'debtorAccount', 'creditorAccount', 'user']),
                201
            );
        });
    }

    /**
     * Display the specified registry entry.
     */
    public function show(RegistryEntry $registry)
    {
        return response()->json(
            $registry->load(['currency', 'debtorAccount', 'creditorAccount', 'user'])
        );
    }

    /**
     * Update the specified registry entry.
     */
    public function update(Request $request, RegistryEntry $registry)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric',
            'debtor_account_id' => 'nullable|exists:accounts,id',
            'creditor_account_id' => 'nullable|exists:accounts,id',
            'commission_1' => 'nullable|numeric',
            'commission_2' => 'nullable|numeric',
            'sender' => 'nullable|string|max:255',
            'receiver' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['commission_1'] = $validated['commission_1'] ?? 0;
        $validated['commission_2'] = $validated['commission_2'] ?? 0;

        return DB::transaction(function () use ($registry, $validated) {
            // Delete old journal entries (Triggering Events)
            $registry->journalEntries->each->delete();

            $registry->update($validated);

            // 1. Debtor Leg (Customer pays principal + all commissions)
            if ($registry->debtor_account_id) {
                $totalToCollect = (float)$registry->amount + (float)$registry->commission_1 + (float)$registry->commission_2;
                JournalService::record($registry, $registry->debtor_account_id, $registry->currency_id, $totalToCollect, 0, "پسوڵەی #{$registry->id} (نوێکراوە) - مەدین (کۆی گشتی)", $registry->entry_date);
            }

            // 2. Creditor Leg (We owe agent: Principal + Commission 2)
            if ($registry->creditor_account_id) {
                $totalWeOweAgent = (float)$registry->amount + (float)$registry->commission_2;
                JournalService::record($registry, $registry->creditor_account_id, $registry->currency_id, 0, $totalWeOweAgent, "پسوڵەی #{$registry->id} (نوێکراوە) - داین (ئەسڵ + عمولەی ٢)", $registry->entry_date);
            }

            // 3. Our Profit (Commission 1 -> Goes to Profit & Loss 02)
            if ($registry->commission_1 > 0) {
                $profitAccount = Account::where('code', '02')->first();
                if ($profitAccount) {
                    JournalService::record($registry, $profitAccount->id, $registry->currency_id, 0, (float)$registry->commission_1, "قازانجی حەواڵە (عمولەی ١) - پسوڵەی #{$registry->id} (نوێکراوە)", $registry->entry_date);
                }
            }

            return response()->json(
                $registry->load(['currency', 'debtorAccount', 'creditorAccount', 'user'])
            );
        });
    }

    /**
     * Remove the specified registry entry.
     */
    public function destroy(RegistryEntry $registry)
    {
        return DB::transaction(function () use ($registry) {
            $registry->journalEntries->each->delete();
            $registry->delete();
            return response()->json(null, 204);
        });
    }
}
