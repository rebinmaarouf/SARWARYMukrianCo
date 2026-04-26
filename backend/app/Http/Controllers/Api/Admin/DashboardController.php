<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStats()
    {
        $today = Carbon::today();
        $totalTransactions = Transaction::count();
        $todayTransactions = Transaction::whereDate('created_at', $today)->count();
        $totalAccounts = Account::count();
        
        $transactions = Transaction::where('type', 'sell')->get();
        $totalProfitUSD = 0;
        foreach ($transactions as $t) {
            if ($t->secondary_currency === 'IQD') {
                $totalProfitUSD += $t->profit / 1515;
            } else {
                $totalProfitUSD += $t->profit;
            }
        }

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayProfit = 0;
            $dayTransactions = Transaction::where('type', 'sell')->whereDate('created_at', $date)->get();
            
            foreach ($dayTransactions as $t) {
                if ($t->secondary_currency === 'IQD') {
                    $dayProfit += $t->profit / 1515;
                } else {
                    $dayProfit += $t->profit;
                }
            }

            $chartData[] = [
                'day' => $date->format('D'),
                'profit' => round($dayProfit, 2)
            ];
        }

        return response()->json([
            'total_transactions' => $totalTransactions,
            'today_transactions' => $todayTransactions,
            'total_accounts' => $totalAccounts,
            'total_profit_usd' => round($totalProfitUSD, 2),
            'chart_data' => $chartData
        ]);
    }
}
