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
        // Expenses (IUAS Code 3)
        $expenseIQD = $this->calculateSumByCode('3%');
        $netProfitIQD = $revenueIQD - $expenseIQD;

        // 2. Real-time Vault Monitor
        $vaultBalances = DB::table('journal_entries')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->join('currencies', 'journal_entries.currency_id', '=', 'currencies.id')
            ->where('accounts.type', 'vault')
            ->select(
                'accounts.name as account_name',
                'currencies.code as currency_code',
                DB::raw('SUM(debit - credit) as balance')
            )
            ->groupBy('accounts.id', 'accounts.name', 'currencies.id', 'currencies.code')
            ->get();

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

    private function calculateSumByCode($codePattern)
    {
        // We sum across all currencies, converting each to base (IQD)
        $entries = JournalEntry::whereHas('account', function($q) use ($codePattern) {
            $q->where('code', 'LIKE', $codePattern);
        })->get();

        return $entries->reduce(function($acc, $entry) {
            // In IUAS, Revenue/Expense balance = Credit - Debit
            $net = $entry->credit - $entry->debit;
            // Get rate for this specific currency
            $rate = $entry->currency->current_rate ?? 1.0;
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
            
            $query = JournalEntry::whereBetween('date', [
                $period === '1y' ? $date->startOfMonth()->toDateString() : $date->toDateString(),
                $period === '1y' ? $date->endOfMonth()->toDateString() : $date->toDateString()
            ]);

            $revenue = (clone $query)->whereHas('account', function($q) {
                $q->where('code', 'LIKE', '4%');
            })->sum(DB::raw('credit - debit'));

            $expense = (clone $query)->whereHas('account', function($q) {
                $q->where('code', 'LIKE', '3%');
            })->sum(DB::raw('debit - credit'));

            $data[] = [
                'label' => $date->format($format),
                'revenue' => round($revenue / $rate, 2),
                'expense' => round($expense / $rate, 2),
                'profit' => round(($revenue - $expense) / $rate, 2)
            ];
        }
        return $data;
    }
}
