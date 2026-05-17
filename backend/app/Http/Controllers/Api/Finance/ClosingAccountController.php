<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\Currency;

class ClosingAccountController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $branchId = $request->input('branch_id');

        $user = auth()->user();
        // Branch isolation for managers
        if ($user && !$user->hasRole('Super Admin') && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        $query = DB::table('journal_entries')
            ->where('date', '<=', $date)
            ->whereNull('deleted_at');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $balances = $query->select('account_id', 'currency_id', DB::raw('SUM(debit - credit) as balance'))
            ->groupBy('account_id', 'currency_id')
            ->get();

        $accounts = Account::withoutGlobalScope('branch')->get()->keyBy('id');

        $result = $balances->map(function ($b) use ($accounts) {
            $account = $accounts->get($b->account_id);
            return [
                'account_id' => $b->account_id,
                'account_name' => $account->name ?? '???',
                'account_code' => $account->code ?? '',
                'account_type' => $account->type ?? '',
                'currency_id' => $b->currency_id,
                'balance' => (float)$b->balance,
            ];
        });

        // Separate by Currency
        $grouped = $result->groupBy('currency_id')->map(function ($items, $currId) {
            $currency = Currency::find($currId);
            
            // Separate into Debits and Credits
            $debits = $items->filter(function ($item) {
                // Assets, Expenses, or positive balance
                return $item['balance'] > 0 || in_array($item['account_type'], ['vault', 'expense']);
            })->values();

            $credits = $items->filter(function ($item) {
                // Liabilities, Equity, Revenue, or negative balance
                return $item['balance'] < 0 || in_array($item['account_type'], ['equity', 'revenue']);
            })->map(function ($item) {
                $item['balance'] = abs($item['balance']); // Make positive for display
                return $item;
            })->values();

            return [
                'currency_id' => $currId,
                'currency_code' => $currency->code ?? '???',
                'currency_symbol' => $currency->symbol ?? '',
                'debits' => $debits,
                'credits' => $credits,
                'total_debits' => $debits->sum('balance'),
                'total_credits' => $credits->sum('balance'),
            ];
        });

        return response()->json($grouped->values());
    }
}
