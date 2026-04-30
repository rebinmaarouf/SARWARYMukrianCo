<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    /**
     * Unified Financial Report (Iraqi Unified Accounting System Style)
     * Groups: 1 (Assets), 2 (Liabilities/Equity), 3 (Expenses), 4 (Revenues)
     */
    public function getUnifiedReport(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month'); // If null, returns yearly

        $query = JournalEntry::query();

        if ($month) {
            $query->whereYear('date', $year)->whereMonth('date', $month);
        } else {
            $query->whereYear('date', $year);
        }

        // Aggregate by Account and Currency
        $data = $query->select(
                'account_id',
                'currency_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit'),
                DB::raw('SUM(debit - credit) as net_balance')
            )
            ->groupBy('account_id', 'currency_id')
            ->with(['account', 'currency'])
            ->get();

        // Group by Unified System Categories
        $report = [
            'assets' => [],      // Group 1
            'liabilities' => [], // Group 2
            'expenses' => [],    // Group 3
            'revenues' => [],    // Group 4
            'summary' => [
                'total_revenue' => [],
                'total_expense' => [],
                'net_profit' => []
            ]
        ];

        foreach ($data as $entry) {
            $code = $entry->account->code;
            $category = 'other';

            if (str_starts_with($code, '1') || $entry->account->type === 'vault') $category = 'assets';
            elseif (str_starts_with($code, '2') || $entry->account->type === 'equity') $category = 'liabilities';
            elseif (str_starts_with($code, '3') || $entry->account->type === 'expense') $category = 'expenses';
            elseif (str_starts_with($code, '4') || $entry->account->type === 'revenue') $category = 'revenues';

            if ($category !== 'other') {
                $report[$category][] = $entry;
                
                // Aggregate Summary
                $currencyCode = $entry->currency->code;
                if ($category === 'revenues') {
                    $report['summary']['total_revenue'][$currencyCode] = ($report['summary']['total_revenue'][$currencyCode] ?? 0) + ($entry->total_credit - $entry->total_debit);
                } elseif ($category === 'expenses') {
                    $report['summary']['total_expense'][$currencyCode] = ($report['summary']['total_expense'][$currencyCode] ?? 0) + ($entry->total_debit - $entry->total_credit);
                }
            }
        }

        // Calculate Net Profit
        foreach ($report['summary']['total_revenue'] as $curr => $val) {
            $exp = $report['summary']['total_expense'][$curr] ?? 0;
            $report['summary']['net_profit'][$curr] = $val - $exp;
        }

        return response()->json($report);
    }

    /**
     * Vault Liquidity Monitoring
     */
    public function getVaultLiquidity()
    {
        $vaults = Account::where('type', 'vault')
            ->with('summaries.currency')
            ->get()
            ->map(function($vault) {
                return [
                    'id' => $vault->id,
                    'name' => $vault->name,
                    'code' => $vault->code,
                    'liquidity' => $vault->summaries->map(function($s) {
                        $isLow = false;
                        // Alert Logic: e.g., < 10,000 USD or < 15,000,000 IQD
                        if ($s->currency->code === 'USD' && $s->balance < 10000) $isLow = true;
                        if ($s->currency->code === 'IQD' && $s->balance < 15000000) $isLow = true;

                        return [
                            'currency' => $s->currency->code,
                            'balance' => $s->balance,
                            'is_low' => $isLow
                        ];
                    })
                ];
            });

        return response()->json($vaults);
    }
}
