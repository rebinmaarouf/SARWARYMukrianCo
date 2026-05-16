<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AgingDebtController extends Controller
{
    /**
     * Get a list of clients who owe money (Debit balance) along with their aging status.
     */
    public function index(Request $request)
    {
        // We only care about accounts that are 'client' or 'supplier' (people who might owe us)
        $accounts = Account::whereIn('type', ['client', 'supplier', 'other'])->get();

        $agingDebts = [];
        $today = \Carbon\Carbon::now()->startOfDay();

        foreach ($accounts as $account) {
            // Get current overall balance in primary currency (or default to USD for now)
            // Or better, aggregate all currencies they owe
            $query = DB::table('journal_entries')
                ->join('currencies', 'journal_entries.currency_id', '=', 'currencies.id')
                ->where('journal_entries.account_id', $account->id)
                ->whereNull('journal_entries.deleted_at');

            if (auth()->check() && auth()->user()->branch_id && !auth()->user()->hasRole('Super Admin')) {
                $query->where('journal_entries.branch_id', auth()->user()->branch_id);
            }

            $balances = $query->select(
                    'currencies.code as currency',
                    DB::raw('SUM(journal_entries.debit - journal_entries.credit) as balance')
                )
                ->groupBy('currencies.code')
                ->having('balance', '>', 0) // Only Debit balances (They owe us)
                ->get();

            if ($balances->isEmpty()) {
                continue; // They don't owe anything
            }

            // Find their earliest due date from payment vouchers
            $vouchQuery = DB::table('vouchers')
                ->where('account_id', $account->id)
                ->where('type', 'payment')
                ->whereNotNull('due_date');

            if (auth()->check() && auth()->user()->branch_id && !auth()->user()->hasRole('Super Admin')) {
                $vouchQuery->where('branch_id', auth()->user()->branch_id);
            }

            $dueVoucher = $vouchQuery->orderBy('due_date', 'asc')->first();

            $dueDate = $dueVoucher ? \Carbon\Carbon::parse($dueVoucher->due_date)->startOfDay() : null;
            $daysOverdue = 0;
            $status = 'green'; // Safe

            if ($dueDate) {
                if ($dueDate->isPast()) {
                    $status = 'red'; // Overdue!
                    $daysOverdue = $today->diffInDays($dueDate);
                } elseif ($dueDate->diffInDays($today) <= 3) {
                    $status = 'yellow'; // Due soon
                }
            } else {
                // If no explicit due date is set, maybe they had a debt for over 30 days?
                // Get the earliest transaction date that put them in debt
                $txQuery = DB::table('journal_entries')
                    ->where('account_id', $account->id)
                    ->whereNull('deleted_at');

                if (auth()->check() && auth()->user()->branch_id && !auth()->user()->hasRole('Super Admin')) {
                    $txQuery->where('branch_id', auth()->user()->branch_id);
                }

                $earliestTx = $txQuery->orderBy('date', 'asc')->first();
                
                if ($earliestTx) {
                    $txDate = \Carbon\Carbon::parse($earliestTx->date)->startOfDay();
                    $daysSinceDebt = $today->diffInDays($txDate);
                    
                    if ($daysSinceDebt > 30) {
                        $status = 'red';
                        $daysOverdue = $daysSinceDebt - 30; // Approximation
                    } elseif ($daysSinceDebt > 25) {
                        $status = 'yellow';
                    }
                }
            }

            $agingDebts[] = [
                'account_id' => $account->id,
                'account_name' => $account->name,
                'account_code' => $account->code,
                'balances' => $balances,
                'due_date' => $dueDate ? $dueDate->format('Y-m-d') : null,
                'status' => $status,
                'days_overdue' => $daysOverdue
            ];
        }

        // Sort by status (red first, then yellow, then green)
        usort($agingDebts, function ($a, $b) {
            $order = ['red' => 1, 'yellow' => 2, 'green' => 3];
            return $order[$a['status']] <=> $order[$b['status']];
        });

        return response()->json($agingDebts);
    }
}
