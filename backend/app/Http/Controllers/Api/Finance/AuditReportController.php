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
        $branchId = $request->input('branch_id'); // null or 'all' for consolidated
        
        // Get latest market rate for USD (Currency ID 2)
        $latestRate = DB::table('exchange_rates')->where('currency_id', 2)->latest()->value('rate') ?? 1500;

        // 1. Assets (Group 1 + Vaults)
        $assets = $this->getAggregatesByGroup('1%', $fromDate, $toDate, $branchId);

        // 2. Liabilities & Equity (Group 2)
        $liabilities = $this->getAggregatesByGroup('2%', $fromDate, $toDate, $branchId);

        // 3. Revenues (Group 4)
        $revenues = $this->getAggregatesByGroup('4%', $fromDate, $toDate, $branchId);

        // 4. Expenses (Group 3 & 5)
        $expenses = $this->getAggregatesByGroup(['3%', '5%'], $fromDate, $toDate, $branchId);

        // 5. Detailed Vault Status (Filtering by branch if requested)
        $vaultQuery = DB::table('account_summaries')
            ->join('accounts', 'account_summaries.account_id', '=', 'accounts.id')
            ->join('currencies', 'account_summaries.currency_id', '=', 'currencies.id')
            ->where('accounts.type', 'vault');

        if ($branchId && $branchId !== 'all') {
            $vaultQuery->where('accounts.branch_id', $branchId);
        }

        $vaults = $vaultQuery->select(
                'accounts.name',
                'accounts.code',
                'currencies.code as currency_code',
                DB::raw('(total_debit - total_credit) as balance')
            )
            ->get();

        // 6. Vault Forensics (Flow of money: In/Out within the period)
        $forensicsQuery = DB::table('journal_entries')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->join('currencies', 'journal_entries.currency_id', '=', 'currencies.id')
            ->whereNull('journal_entries.deleted_at')
            ->where('accounts.type', 'vault')
            ->whereBetween('journal_entries.date', [$fromDate, $toDate]);

        if ($branchId && $branchId !== 'all') {
            $forensicsQuery->where('accounts.branch_id', $branchId);
        }

        $vaultForensics = $forensicsQuery->select(
                'accounts.name as vault_name',
                'accounts.code as vault_code',
                'currencies.code as currency_code',
                DB::raw('SUM(debit) as total_in'),
                DB::raw('SUM(credit) as total_out'),
                DB::raw('SUM(debit - credit) as net_change')
            )
            ->groupBy('accounts.id', 'accounts.name', 'accounts.code', 'currencies.code')
            ->get();

        // 7. Vault Forensic Details (Raw list of entries that make up the In and Out totals)
        $forensicsDetailsQuery = DB::table('journal_entries')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->join('currencies', 'journal_entries.currency_id', '=', 'currencies.id')
            ->leftJoin('users', 'journal_entries.user_id', '=', 'users.id')
            ->whereNull('journal_entries.deleted_at')
            ->where('accounts.type', 'vault')
            ->whereBetween('journal_entries.date', [$fromDate, $toDate]);

        if ($branchId && $branchId !== 'all') {
            $forensicsDetailsQuery->where('accounts.branch_id', $branchId);
        }

        $vaultDetails = $forensicsDetailsQuery->select(
                'journal_entries.id',
                'accounts.code as vault_code',
                'currencies.code as currency_code',
                'journal_entries.date',
                'journal_entries.debit as total_in',
                'journal_entries.credit as total_out',
                'journal_entries.description',
                'users.name as user_name',
                'journal_entries.created_at'
            )
            ->orderBy('journal_entries.created_at', 'desc')
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
            'vault_forensics' => $vaultForensics,
            'vault_details' => $vaultDetails,
            'exchange_rate' => $latestRate
        ]);
    }

    private function getAggregatesByGroup($patterns, $from, $to, $branchId = null)
    {
        if (!is_array($patterns)) $patterns = [$patterns];

        $accountQuery = Account::withoutGlobalScopes()->where(function($q) use ($patterns) {
            foreach($patterns as $p) {
                $q->orWhere('code', 'LIKE', $p);
            }
        });

        if (in_array('1%', $patterns)) {
            $accountQuery->orWhere('type', 'vault');
        }

        if ($branchId && $branchId !== 'all') {
            $accountQuery->where('branch_id', $branchId);
        }

        $accounts = $accountQuery->get();

        $totalIQD = 0;
        $details = [];

        foreach ($accounts as $account) {
            // For Groups 1 & 2 (Balance Sheet): We look at the cumulative balance up to $to
            // For Groups 3 & 4 (P&L): We look at activity BETWEEN $from and $to
            $isPL = str_starts_with($account->code, '3') || str_starts_with($account->code, '4') || str_starts_with($account->code, '5');

            $query = DB::table('journal_entries')
                ->where('account_id', $account->id)
                ->whereNull('deleted_at');

            if ($isPL) {
                $query->whereBetween('date', [$from, $to]);
            } else {
                $query->where('date', '<=', $to);
            }

            $entries = $query->select(
                    'currency_id', 
                    DB::raw('SUM(debit) as total_debit'), 
                    DB::raw('SUM(credit) as total_credit')
                )
                ->groupBy('currency_id')
                ->get();

            $accountTotalIQD = 0;
            foreach ($entries as $e) {
                $currency = DB::table('currencies')->where('id', $e->currency_id)->first();
                $rate = $currency->exchange_rate_to_base ?? 1.0;

                $code = $account->code;
                // Standard IUAS Balance Calculation
                if (str_starts_with($code, '1') || str_starts_with($code, '3') || str_starts_with($code, '5') || $account->type === 'vault') {
                    $balance = $e->total_debit - $e->total_credit;
                } else {
                    $balance = $e->total_credit - $e->total_debit;
                }
                $accountTotalIQD += ($balance * $rate);
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
