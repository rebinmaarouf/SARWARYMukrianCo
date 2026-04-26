<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['account', 'user'])->latest();
        
        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        return response()->json($query->paginate(50));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'type' => 'required|in:buy,sell',
                'pair' => 'required|string',
                'primary_currency' => 'required|string',
                'primary_amount' => 'required|numeric',
                'secondary_currency' => 'required|string',
                'secondary_amount' => 'required|numeric',
                'rate' => 'required|numeric',
                'note' => 'nullable|string',
                'client_name' => 'nullable|string'
            ]);

            return DB::transaction(function () use ($request) {
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'account_id' => $request->account_id,
                    'type' => $request->type,
                    'pair' => $request->pair,
                    'primary_currency' => $request->primary_currency,
                    'primary_amount' => $request->primary_amount,
                    'secondary_currency' => $request->secondary_currency,
                    'secondary_amount' => $request->secondary_amount,
                    'rate' => $request->rate,
                    'profit' => $this->calculateProfit($request),
                    'client_name' => $request->client_name,
                    'note' => $request->note
                ]);

                return response()->json($transaction->load('account'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProfitReport(Request $request)
    {
        $accountId = $request->account_id;
        $startDate = $request->start_date . ' 00:00:00';
        $endDate = $request->end_date . ' 23:59:59';

        // 1. Calculate Opening Balance (Everything before start_date)
        $openingBalanceQuery = Transaction::where('created_at', '<', $startDate);
        if ($accountId) $openingBalanceQuery->where('account_id', $accountId);
        
        $openingBalances = $openingBalanceQuery->get()->groupBy('primary_currency')->map(function ($group) {
            return $group->where('type', 'buy')->sum('primary_amount') - $group->where('type', 'sell')->sum('primary_amount');
        });

        // 2. Fetch Period Transactions
        $periodQuery = Transaction::query();
        if ($accountId) $periodQuery->where('account_id', $accountId);
        $periodQuery->whereBetween('created_at', [$startDate, $endDate]);
        
        $transactions = $periodQuery->latest()->get();

        $totalProfitIQD = 0;
        $currenciesData = $transactions->groupBy('primary_currency')->map(function ($group, $currency) use (&$totalProfitIQD, $openingBalances) {
            $buyAmount = $group->where('type', 'buy')->sum('primary_amount');
            $sellAmount = $group->where('type', 'sell')->sum('primary_amount');
            
            // Current Period Balance
            $periodBalance = $buyAmount - $sellAmount;
            // Total Balance = Opening + Period
            $finalBalance = ($openingBalances[$currency] ?? 0) + $periodBalance;

            foreach ($group as $t) {
                if ($t->type === 'sell') {
                    if ($t->secondary_currency === 'IQD') $totalProfitIQD += $t->profit;
                    else $totalProfitIQD += $t->profit * 1515; 
                }
            }

            return [
                'opening_balance' => $openingBalances[$currency] ?? 0,
                'period_balance' => $periodBalance,
                'final_balance' => $finalBalance,
                'total_buy' => $buyAmount,
                'total_sell' => $sellAmount,
                'profit' => $group->sum('profit'),
                'currency' => $currency,
                'secondary_symbol' => $group->first()->secondary_currency
            ];
        });

        return response()->json([
            'total_profit_iqd' => round($totalProfitIQD),
            'total_profit_usd' => round($totalProfitIQD / 1515, 2),
            'transaction_count' => $transactions->count(),
            'currencies' => $currenciesData,
            'transactions' => $transactions->load('account')
        ]);
    }

    public function show($id)
    {
        return response()->json(Transaction::with('account')->findOrFail($id));
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted successfully']);
    }

    private function calculateProfit($request)
    {
        $type = $request->type;
        $primary = $request->primary_currency;
        $secondary = $request->secondary_currency;
        $rate = (float)$request->rate;
        $amount = (float)$request->primary_amount;

        // Buying has 0 realized profit
        if ($type === 'buy') {
            return 0;
        }

        // For Selling: We need to find what we bought it for (Average Cost)
        // Let's find the latest "buy" rate for this currency in this account
        $lastBuyRate = Transaction::where('account_id', $request->account_id)
            ->where('type', 'buy')
            ->where('primary_currency', $primary)
            ->where('secondary_currency', $secondary)
            ->latest()
            ->value('rate');

        // If we found a previous buy rate, use it to calculate real profit
        if ($lastBuyRate) {
            // Profit = (Sell Rate - Buy Rate) * (Amount / 100)
            // Example: (152,000 - 151,000) * (100 / 100) = 1,000 IQD
            return ($rate - $lastBuyRate) * ($amount / 100);
        }

        // Fallback: If no history, assume a standard 500 IQD spread per $100
        return ($amount / 100) * 500;
    }
}
