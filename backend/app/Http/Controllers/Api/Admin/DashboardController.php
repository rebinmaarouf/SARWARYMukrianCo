<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\AccountSummary;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats()
    {
        $today = Carbon::today();
        
        // Basic Stats
        $totalTransactions = Transaction::count();
        $todayTransactions = Transaction::whereDate('created_at', $today)->count();
        $totalAccounts = Account::count();
        
        // Profit calculation from JournalEntries (Revenue accounts)
        // In the Unified System, Revenue is usually Code 4
        $totalProfitIQD = JournalEntry::whereHas('account', function($q) {
            $q->where('code', 'LIKE', '4%')->orWhere('type', 'revenue');
        })->sum(DB::raw('credit - debit'));

        // For dashboard display, we might want a USD equivalent
        // Using a recent rate if available, otherwise fallback
        $latestRate = DB::table('exchange_rates')->where('currency_id', 2)->latest()->value('rate') ?? 1500;
        $totalProfitUSD = $totalProfitIQD / $latestRate;

        // Real-time Vault Balances (Per Currency)
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

        // Chart Data (Profit over last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            $dayProfitIQD = JournalEntry::whereDate('date', $date)
                ->whereHas('account', function($q) {
                    $q->where('code', 'LIKE', '4%')->orWhere('type', 'revenue');
                })->sum(DB::raw('credit - debit'));

            $chartData[] = [
                'day' => $date->format('D'),
                'profit' => round($dayProfitIQD / $latestRate, 2)
            ];
        }

        return response()->json([
            'total_transactions' => $totalTransactions,
            'today_transactions' => $todayTransactions,
            'total_accounts' => $totalAccounts,
            'total_profit_usd' => round($totalProfitUSD, 2),
            'vault_balances' => $vaultBalances,
            'chart_data' => $chartData
        ]);
    }
}
