<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Currency;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Enhanced Financial Analytics based on Iraqi Unified Accounting System (IUAS)
     */
    public function getStats(Request $request)
    {
        $period = $request->input('period', '7d'); // 7d, 30d, 1y
        $latestRate = DB::table('exchange_rates')->where('currency_id', 2)->latest()->value('rate') ?? 1500;
        
        // 1. Core Financial Aggregates (Valued in IQD)
        // Revenue (IUAS Code 4)
        $revenueIQD = $this->calculateSumByCode('4%');
        // Expenses (IUAS Code 3 and 5)
        $expenseIQD = $this->calculateSumByCode(['3%', '5%']);
        $netProfitIQD = $revenueIQD - $expenseIQD;

        // 2. Real-time Vault Monitor (Branch-Aware)
        $vaultBalances = $this->getVaultBalances();

        // 3. Time-Series Analytics (Revenue vs Expense)
        $chartData = $this->getChartData($period, $latestRate);

        return response()->json([
            'summary' => [
                'revenue_iqd' => round($revenueIQD, 2),
                'expense_iqd' => round($expenseIQD, 2),
                'net_profit_iqd' => round($netProfitIQD, 2),
                'net_profit_usd' => round($netProfitIQD / $latestRate, 2),
            ],
            'vault_balances' => $vaultBalances,
            'chart_data' => $chartData,
            'meta' => [
                'exchange_rate' => $latestRate,
                'period' => $period,
                'total_accounts' => Account::count(),
                'today_ops' => JournalEntry::where('date', Carbon::today()->toDateString())->count()
            ]
        ]);
    }

    private function calculateSumByCode($codePatterns)
    {
        if (!is_array($codePatterns)) $codePatterns = [$codePatterns];

        $query = JournalEntry::whereHas('account', function($q) use ($codePatterns) {
            $q->where(function($sub) use ($codePatterns) {
                foreach($codePatterns as $pattern) {
                    $sub->orWhere('code', 'LIKE', $pattern);
                }
            });
        });

        $entries = $query->with('currency')->get();

        return $entries->reduce(function($acc, $entry) {
            $code = $entry->account->code;
            
            // In IUAS:
            // Revenue (4): Balance = Credit - Debit
            // Expense (3/5): Balance = Debit - Credit
            if (str_starts_with($code, '4')) {
                $net = $entry->credit - $entry->debit;
            } else {
                $net = $entry->debit - $entry->credit;
            }

            $rate = $entry->rate_at_time ?? 1.0;
            return $acc + ($net * $rate);
        }, 0);
    }

    private function getChartData($period, $rate)
    {
        $data = [];
        $count = $period === '1y' ? 12 : ($period === '30d' ? 30 : 7);
        $format = $period === '1y' ? 'M' : 'D';
        
        for ($i = $count - 1; $i >= 0; $i--) {
            $date = $period === '1y' ? Carbon::today()->subMonths($i) : Carbon::today()->subDays($i);
            
            $startDate = $period === '1y' ? $date->startOfMonth()->toDateString() : $date->toDateString();
            $endDate = $period === '1y' ? $date->endOfMonth()->toDateString() : $date->toDateString();

            $revQuery = JournalEntry::whereBetween('date', [$startDate, $endDate])
                ->whereHas('account', function($q) { $q->where('code', 'LIKE', '4%'); });

            $expQuery = JournalEntry::whereBetween('date', [$startDate, $endDate])
                ->whereHas('account', function($q) { 
                    $q->where('code', 'LIKE', '3%')->orWhere('code', 'LIKE', '5%'); 
                });

            $revenue = $revQuery->sum(DB::raw('credit - debit'));
            $expense = $expQuery->sum(DB::raw('debit - credit'));

            $data[] = [
                'label' => $date->format($format),
                'revenue' => round($revenue / $rate, 2),
                'expense' => round($expense / $rate, 2),
                'profit' => round(($revenue - $expense) / $rate, 2)
            ];
        }
        return $data;
    }
    private function getVaultBalances()
    {
        $vaultQuery = Account::with('summaries.currency')
            ->where('type', 'vault');

        $balances = collect();
        foreach ($vaultQuery->get() as $account) {
            foreach ($account->summaries as $summary) {
                $balances->push([
                    'account_name' => $account->name,
                    'currency_code' => $summary->currency->code,
                    'balance' => $summary->total_debit - $summary->total_credit
                ]);
            }
        }
        return $balances;
    }
}
