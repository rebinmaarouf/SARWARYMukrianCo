<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Account;

class SmartAnalyticsService
{
    /**
     * Compute cash flow projections and detect financial anomalies in a single scan.
     */
    public static function getLiquidityAndAnomalies($branchId = null)
    {
        // 1. Get Current physical vault balances
        $vaultBalancesQuery = DB::table('account_summaries')
            ->join('accounts', 'account_summaries.account_id', '=', 'accounts.id')
            ->join('currencies', 'account_summaries.currency_id', '=', 'currencies.id')
            ->where('accounts.type', 'vault');

        if ($branchId && $branchId !== 'all') {
            $vaultBalancesQuery->where('accounts.branch_id', $branchId);
        }

        $balances = $vaultBalancesQuery->select(
            'currencies.code as currency_code',
            DB::raw('SUM(total_debit - total_credit) as balance')
        )
        ->groupBy('currencies.code')
        ->pluck('balance', 'currency_code')
        ->toArray();

        // 2. Calculate predictions for USD and IQD
        $predictions = [];
        $currenciesToPredict = ['USD', 'IQD'];

        foreach ($currenciesToPredict as $currency) {
            $currentBalance = (float)($balances[$currency] ?? 0);

            // Fetch last 30 days of daily credit (outflow) for vaults
            $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();
            
            $outflowQuery = DB::table('journal_entries')
                ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
                ->join('currencies', 'journal_entries.currency_id', '=', 'currencies.id')
                ->whereNull('journal_entries.deleted_at')
                ->where('accounts.type', 'vault')
                ->where('currencies.code', $currency)
                ->where('journal_entries.date', '>=', $thirtyDaysAgo);

            if ($branchId && $branchId !== 'all') {
                $outflowQuery->where('accounts.branch_id', $branchId);
            }

            $dailyOutflows = $outflowQuery->select(
                'journal_entries.date',
                DB::raw('SUM(journal_entries.credit) as total_outflow')
            )
            ->groupBy('journal_entries.date')
            ->orderBy('journal_entries.date', 'asc')
            ->get();

            $totalOutflowSum = 0;
            $countDays = count($dailyOutflows);

            foreach ($dailyOutflows as $day) {
                $totalOutflowSum += (float)$day->total_outflow;
            }

            // Calculate daily average velocity with a sensible business fallback if empty
            $avgDailyOutflow = $countDays > 0 ? ($totalOutflowSum / $countDays) : 0;
            if ($avgDailyOutflow <= 0) {
                $avgDailyOutflow = ($currency === 'IQD' ? 3500000 : 2500); // Standard baseline default
            }

            // Compute growth trend (compare last 7 days vs previous 7 days)
            $last7DaysOutflow = 0;
            $prev7DaysOutflow = 0;
            $today = Carbon::today();

            foreach ($dailyOutflows as $day) {
                $dayDate = Carbon::parse($day->date);
                if ($dayDate->between($today->copy()->subDays(7), $today)) {
                    $last7DaysOutflow += (float)$day->total_outflow;
                } elseif ($dayDate->between($today->copy()->subDays(14), $today->copy()->subDays(8))) {
                    $prev7DaysOutflow += (float)$day->total_outflow;
                }
            }

            $slope = 0;
            if ($prev7DaysOutflow > 0) {
                $slope = ($last7DaysOutflow - $prev7DaysOutflow) / $prev7DaysOutflow;
                // Clip slope to keep predictions realistic
                $slope = max(-0.4, min(0.4, $slope));
            }

            // Forecasted weekly outflow = avg daily * 7 days * (1 + slope)
            $predicted7dOutflow = $avgDailyOutflow * 7 * (1 + $slope);

            // Determine safety status
            $ratio = $predicted7dOutflow > 0 ? ($currentBalance / $predicted7dOutflow) : 99;
            $status = 'secure';
            $statusKurdish = '🟢 بێ کێشە (سیولەی تەواو)';
            
            if ($ratio < 1.0) {
                $status = 'critical';
                $statusKurdish = '🔴 هۆشداری کەمبوونی توند';
            } elseif ($ratio < 1.5) {
                $status = 'warning';
                $statusKurdish = '🟡 پێویستی بە ئۆردەری سیولەیە';
            }

            // Suggested cash injection to establish safety comfort level
            $suggestedInjection = 0;
            if ($currentBalance < ($predicted7dOutflow * 1.5)) {
                $suggestedInjection = ($predicted7dOutflow * 1.6) - $currentBalance;
            }

            $predictions[$currency] = [
                'current_balance' => $currentBalance,
                'avg_daily_outflow' => $avgDailyOutflow,
                'trend_slope' => round($slope * 100, 1), // percentage
                'predicted_7d_outflow' => $predicted7dOutflow,
                'status' => $status,
                'status_kurdish' => $statusKurdish,
                'suggested_injection' => max(0, $suggestedInjection)
            ];
        }

        // 3. Scan anomalies from the last 30 days
        $anomalies = [];
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateTimeString();
        
        $transactionsQuery = DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->leftJoin('users', 'transactions.user_id', '=', 'users.id')
            ->whereNull('transactions.deleted_at')
            ->where('transactions.created_at', '>=', $thirtyDaysAgo);

        if ($branchId && $branchId !== 'all') {
            $transactionsQuery->where('transactions.branch_id', $branchId);
        }

        $transactions = $transactionsQuery->select(
            'transactions.id',
            'transactions.type',
            'transactions.pair',
            'transactions.primary_currency',
            'transactions.primary_amount',
            'transactions.secondary_currency',
            'transactions.secondary_amount',
            'transactions.rate',
            'transactions.profit',
            'transactions.created_at',
            'accounts.name as account_name',
            'users.name as operator_name'
        )
        ->orderBy('transactions.created_at', 'desc')
        ->get();

        foreach ($transactions as $t) {
            $createdTime = Carbon::parse($t->created_at);
            $hour = $createdTime->hour;

            // Anomaly 1: Late-night transaction (between 10 PM and 6 AM)
            if ($hour >= 22 || $hour < 6) {
                $anomalies[] = [
                    'id' => $t->id,
                    'type' => 'transaction',
                    'date' => $createdTime->toDateTimeString(),
                    'primary_amount' => (float)$t->primary_amount,
                    'primary_currency' => $t->primary_currency,
                    'rate' => (float)$t->rate,
                    'operator' => $t->operator_name ?? 'سیستم',
                    'category' => 'کاتژمێری نەگونجاو',
                    'description' => "ئەم مامەڵەیە لە کاتژمێر " . $createdTime->format('h:i A') . "ی شەودا تۆمارکراوە کە دەرەوەی کاتی دەوامی فەرمییە.",
                    'severity' => 'medium',
                    'severity_kurdish' => '🟡 مامناوەند'
                ];
            }

            // Anomaly 2: Abnormal exchange rate divergent from 1500 benchmark (+/- 10%)
            if ($t->pair === 'USD/IQD' || ($t->primary_currency === 'USD' && $t->secondary_currency === 'IQD')) {
                $rate = (float)$t->rate;
                $normalizedRate = $rate > 10000 ? ($rate / 100) : $rate;
                if ($normalizedRate > 1650 || $normalizedRate < 1350) {
                    $anomalies[] = [
                        'id' => $t->id,
                        'type' => 'transaction',
                        'date' => $createdTime->toDateTimeString(),
                        'primary_amount' => (float)$t->primary_amount,
                        'primary_currency' => $t->primary_currency,
                        'rate' => $rate,
                        'operator' => $t->operator_name ?? 'سیستم',
                        'category' => 'نرخی ناڕێک',
                        'description' => "نرخی ئاڵوگۆڕی تۆمارکراو ($rate) جیاوازییەکی زۆری هەیە لەگەڵ نرخی هاوسەنگی جێگیر.",
                        'severity' => 'high',
                        'severity_kurdish' => '🔴 بەرز'
                    ];
                }
            }

            // Anomaly 3: Extreme value outlier ($30,000 USD or 45,000,000 IQD)
            $pAmount = (float)$t->primary_amount;
            if (($t->primary_currency === 'USD' && $pAmount >= 30000) || ($t->primary_currency === 'IQD' && $pAmount >= 45000000)) {
                $anomalies[] = [
                    'id' => $t->id,
                    'type' => 'transaction',
                    'date' => $createdTime->toDateTimeString(),
                    'primary_amount' => $pAmount,
                    'primary_currency' => $t->primary_currency,
                    'rate' => (float)$t->rate,
                    'operator' => $t->operator_name ?? 'سیستم',
                    'category' => 'قەبارەی زۆر گەورە',
                    'description' => "بڕی مامەڵەکە (" . number_format($pAmount) . " " . $t->primary_currency . ") زۆر گەورەیە و پێویستی بە دڵنیابوونەوەی تایبەت هەیە.",
                    'severity' => 'critical',
                    'severity_kurdish' => '🚨 یەکجار بەرز'
                ];
            }

            // Anomaly 4: High Negative profit (Dynamic currency detection based on secondary currency)
            $profit = (float)$t->profit;
            $secCurrency = $t->secondary_currency ?? 'IQD';
            
            $isAnomalyLoss = false;
            if ($secCurrency === 'USD') {
                $isAnomalyLoss = ($profit < -50); // Flag losses greater than $50 USD
            } else {
                $isAnomalyLoss = ($profit < -1000); // Flag losses greater than 1,000 IQD
            }

            if ($isAnomalyLoss) {
                $currencyLabel = ($secCurrency === 'USD') ? 'دۆلار' : 'دینار';
                $anomalies[] = [
                    'id' => $t->id,
                    'type' => 'transaction',
                    'date' => $createdTime->toDateTimeString(),
                    'primary_amount' => (float)$t->primary_amount,
                    'primary_currency' => $t->primary_currency,
                    'rate' => (float)$t->rate,
                    'operator' => $t->operator_name ?? 'سیستم',
                    'category' => 'زیانی ئاڵوگۆڕ',
                    'description' => "ئەم ئاڵوگۆڕە بڕی زیانێکی زۆری تۆمارکردووە (" . number_format(abs($profit)) . " $currencyLabel). تکایە پشکنین بۆ دروستی حیساب بکەن.",
                    'severity' => 'high',
                    'severity_kurdish' => '🔴 بەرز'
                ];
            }
        }

        return [
            'predictions' => $predictions,
            'anomalies' => $anomalies
        ];
    }
}
