<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    /**
     * Display a listing of general ledger entries.
     */
    public function index(Request $request)
    {
        $query = JournalEntry::with(['account', 'currency', 'user', 'entryable'])
            ->latest('date')
            ->latest('id');

        if ($accountId = $request->input('account_id')) {
            $account = \App\Models\Account::withoutGlobalScopes()->find($accountId);
            
            // If it's a global account (branch_id is null), we show all transactions across branches
            if ($account && is_null($account->branch_id)) {
                $query->withoutGlobalScopes();
            }

            // Check if this account has children
            $childIds = \App\Models\Account::withoutGlobalScopes()->where('parent_id', $accountId)->pluck('id');
            
            if ($childIds->isNotEmpty()) {
                $query->whereIn('account_id', $childIds->push($accountId));
            } else {
                $query->where('account_id', $accountId);
            }
        }

        if ($currencyId = $request->input('currency_id')) {
            $query->where('currency_id', $currencyId);
        }

        if ($startDate = $request->input('start_date')) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->where('date', '<=', $endDate);
        }

        return response()->json($query->paginate($request->input('per_page', 50)));
    }

    /**
     * Delete a journal entry (and its parent source)
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('delete journals')) {
            abort(403, 'Unauthorized action. You do not have permission to delete journals.');
        }

        $entry = JournalEntry::findOrFail($id);
        
        // Delete the parent source which will cascade to all related journal entries
        // This ensures the double-entry accounting principle is maintained
        if ($entry->entryable) {
            $entry->entryable->delete();
        } else {
            // Fallback for standalone entries
            $entry->delete();
        }

        return response()->json(['message' => 'Journal entry and its associated transactions deleted successfully']);
    }

    /**
     * Display the specified journal entry.
     */
    public function show($id)
    {
        $entry = JournalEntry::with(['account', 'currency', 'user', 'entryable'])->findOrFail($id);
        return response()->json($entry);
    }
}
