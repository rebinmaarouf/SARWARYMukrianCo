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
            $query->where('account_id', $accountId);
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
}
