<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditReportController extends Controller
{
    public function getAdvancedAudit(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::today()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::today()->toDateString());
        
        // Get latest market rate for USD (Currency ID 2)
        $latestRate = DB::table('exchange_rates')->where('currency_id', 2)->latest()->value('rate') ?? 1500;

        // 1. Assets (IUAS Group 1) - Vaults, Receivables
        $assets = $this->getAggregatesByGroup('1%', $fromDate, $toDate);

        // 2. Liabilities & Equity (IUAS Group 2) - Payables, Capital
        $liabilities = $this->getAggregatesByGroup('2%', $fromDate, $toDate);

        // 3. Revenues (IUAS Group 4)
        $revenues = $this->getAggregatesByGroup('4%', $fromDate, $toDate);

        // 4. Expenses (IUAS Group 3 & 5)
        $expenses = $this->getAggregatesByGroup(['3%', '5%'], $fromDate, $toDate);

        // 5. Detailed Vault Status (Real-time physical cash)
        $vaults = DB::table('account_summaries')
            ->join('accounts', 'account_summaries.account_id', '=', 'accounts.id')
            ->join('currencies', 'account_summaries.currency_id', '=', 'currencies.id')
            ->where('accounts.type', 'vault')
            ->select(
                'accounts.name',
                'accounts.code',
                'currencies.code as currency_code',
                DB::raw('(total_debit - total_credit) as balance')
            )
            ->get();

        return response()->json([
            'period' => [
                'from' => $fromDate,
                'to' => $toDate
            ],
            'financials' => [
                'assets' => $assets,
                'liabilities' => $liabilities,
                'revenues' => $revenues,
                'expenses' => $expenses,
                'net_profit' => $revenues['total_iqd'] - $expenses['total_iqd']
            ],
            'vaults' => $vaults,
            'exchange_rate' => $latestRate
        ]);
    }

    private function getAggregatesByGroup($patterns, $from, $to)
    {
        if (!is_array($patterns)) $patterns = [$patterns];

        $accounts = Account::where(function($q) use ($patterns) {
            foreach($patterns as $p) {
                $q->orWhere('code', 'LIKE', $p);
            }
        })->get();

        $totalIQD = 0;
        $details = [];

        foreach ($accounts as $account) {
            $summaries = DB::table('account_summaries')
                ->where('account_id', $account->id)
                ->join('currencies', 'account_summaries.currency_id', '=', 'currencies.id')
                ->select('currencies.code', 'currencies.exchange_rate_to_base as rate', 'total_debit', 'total_credit')
                ->get();

            $accountTotalIQD = 0;
            foreach ($summaries as $s) {
                // Determine balance based on account type code
                $code = $account->code;
                if (str_starts_with($code, '1') || str_starts_with($code, '3') || str_starts_with($code, '5')) {
                    $balance = $s->total_debit - $s->total_credit;
                } else {
                    $balance = $s->total_credit - $s->total_debit;
                }
                $accountTotalIQD += ($balance * ($s->rate ?? 1.0));
            }

            if ($accountTotalIQD != 0) {
                $details[] = [
                    'name' => $account->name,
                    'code' => $account->code,
                    'type' => $account->type,
                    'balance_iqd' => $accountTotalIQD
                ];
                $totalIQD += $accountTotalIQD;
            }
        }

        return [
            'total_iqd' => $totalIQD,
            'accounts' => $details
        ];
    }
}
