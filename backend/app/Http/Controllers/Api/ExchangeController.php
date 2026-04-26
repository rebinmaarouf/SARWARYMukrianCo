<?php

namespace App\Http\Controllers\Api;

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
            $account = Account::find($request->account_id);
            
            // LOGIC: Moving balances based on transaction
            // If BUY: We receive primary (USD), give secondary (IQD)
            // If SELL: We give primary (USD), receive secondary (IQD)
            
            // Note: Balances logic will be implemented here once we have accounts/balances table
            // For now, we record the transaction and profit calculation
            
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
    }

    private function calculateProfit($request)
    {
        // Simple logic for now: 0.5% of the total transaction as profit
        // Or you can compare it with a "Market Rate"
        $totalValue = $request->secondary_amount;
        return $totalValue * 0.005; // 0.5% profit placeholder
    }

    public function report(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        
        $stats = Transaction::whereBetween('created_at', [$from, $to])
            ->select('primary_currency', DB::raw('SUM(profit) as total_profit'))
            ->groupBy('primary_currency')
            ->get();
            
        return response()->json([
            'from' => $from,
            'to' => $to,
            'profits' => $stats
        ]);
    }
}
