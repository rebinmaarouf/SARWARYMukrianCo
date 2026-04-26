<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\RegistryEntry;
use Illuminate\Http\Request;

class RegistryController extends Controller
{
    /**
     * Display a listing of registry entries.
     * Supports filtering by currency_id, date range, and search.
     */
    public function index(Request $request)
    {
        $query = RegistryEntry::with(['currency', 'debtorAccount', 'creditorAccount', 'user']);

        if ($currencyId = $request->get('currency_id')) {
            $query->where('currency_id', $currencyId);
        }

        if ($from = $request->get('from_date')) {
            $query->whereDate('entry_date', '>=', $from);
        }

        if ($to = $request->get('to_date')) {
            $query->whereDate('entry_date', '<=', $to);
        }

        if ($search = $request->get('search')) {
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
                  ->paginate($request->get('per_page', 50))
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

        $validated['user_id'] = $request->user()->id;

        $entry = RegistryEntry::create($validated);

        return response()->json(
            $entry->load(['currency', 'debtorAccount', 'creditorAccount', 'user']),
            201
        );
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

        $registry->update($validated);

        return response()->json(
            $registry->load(['currency', 'debtorAccount', 'creditorAccount', 'user'])
        );
    }

    /**
     * Remove the specified registry entry.
     */
    public function destroy(RegistryEntry $registry)
    {
        $registry->delete();
        return response()->json(null, 204);
    }
}
