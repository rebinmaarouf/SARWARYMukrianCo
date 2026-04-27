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

            // Record Double-Entry Journal
            if ($entry->debtor_account_id) {
                JournalService::record($entry, $entry->debtor_account_id, $entry->currency_id, (float) $entry->amount, 0, "پسوڵەی ژمارە {$entry->id} - مەدین", $entry->entry_date);
            }

            if ($entry->creditor_account_id) {
                JournalService::record($entry, $entry->creditor_account_id, $entry->currency_id, 0, (float) $entry->amount, "پسوڵەی ژمارە {$entry->id} - داین", $entry->entry_date);
            }

            // Commissions (Revenue)
            if ($entry->commission_1 > 0 || $entry->commission_2 > 0) {
                $revenueAccount = Account::where('type', 'revenue')
                    ->orWhere('code', 'LIKE', '4%')
                    ->first();
                    
                if ($revenueAccount) {
                    $totalCommission = (float)($entry->commission_1 ?? 0) + (float)($entry->commission_2 ?? 0);
                    if ($totalCommission > 0) {
                        JournalService::record($entry, $revenueAccount->id, $entry->currency_id, 0, $totalCommission, "عمولەی پسوڵەی ژمارە {$entry->id}", $entry->entry_date);
                    }
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
            // Delete old journal entries
            $registry->journalEntries()->delete();

            $registry->update($validated);

            // Re-record Journal Entries
            if ($registry->debtor_account_id) {
                JournalService::record($registry, $registry->debtor_account_id, $registry->currency_id, (float) $registry->amount, 0, "پسوڵەی ژمارە {$registry->id} (نوێکراوە) - مەدین", $registry->entry_date);
            }

            if ($registry->creditor_account_id) {
                JournalService::record($registry, $registry->creditor_account_id, $registry->currency_id, 0, (float) $registry->amount, "پسوڵەی ژمارە {$registry->id} (نوێکراوە) - داین", $registry->entry_date);
            }

            // Commissions (Revenue)
            if ($registry->commission_1 > 0 || $registry->commission_2 > 0) {
                $revenueAccount = Account::where('code', 'LIKE', '4%')->orWhere('type', 'revenue')->first();
                if ($revenueAccount) {
                    $totalCommission = ($registry->commission_1 ?? 0) + ($registry->commission_2 ?? 0);
                    JournalService::record($registry, $revenueAccount->id, $registry->currency_id, 0, $totalCommission, "عمولەی پسوڵەی ژمارە {$registry->id}", $registry->entry_date);
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
            $registry->journalEntries()->delete();
            $registry->delete();
            return response()->json(null, 204);
        });
    }
}
