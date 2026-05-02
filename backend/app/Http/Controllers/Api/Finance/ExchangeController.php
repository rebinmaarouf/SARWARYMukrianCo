<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Currency;
use App\Services\JournalService;
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
                // New: Flexible Vault selection
                'vault_from_id' => 'required|exists:accounts,id',
                'vault_to_id' => 'required|exists:accounts,id',
                'note' => 'nullable|string',
                'client_name' => 'nullable|string'
            ]);

            return DB::transaction(function () use ($request) {
                // 1. Check if the Vault has enough balance to perform this operation (The "Scientific" Lock)
                $vaultFrom = Account::find($request->vault_from_id);
                $currencyToGive = $request->type === 'buy' ? $request->secondary_currency : $request->primary_currency;
                $amountToGive = $request->type === 'buy' ? $request->secondary_amount : $request->primary_amount;
                
                $currencyModel = Currency::where('code', $currencyToGive)->first();
                $balance = \App\Models\AccountSummary::where('account_id', $vaultFrom->id)
                    ->where('currency_id', $currencyModel->id)
                    ->first();

                $currentBalance = $balance ? ($balance->total_debit - $balance->total_credit) : 0;

                if ($currentBalance < $amountToGive) {
                    throw new \Exception("سندوقی دەستنیشانکراو بڕی پێویست ({$currencyToGive})ی تیا نییە. هاوسەنگی ئێستا: " . number_format($currentBalance));
                }

                // 2. Calculate Profit (if any)
                $profit = $this->calculateProfit($request);

                // 2. Create Transaction record
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
                    'profit' => $profit,
                    'client_name' => $request->client_name,
                    'note' => $request->note
                ]);

                // 3. Journal Entries Logic (4-Leg Double Entry)
                $this->recordJournalEntries($transaction, $request);

                return response()->json($transaction->load('account'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function recordJournalEntries(Transaction $transaction, Request $request)
    {
        $primaryCurrency = Currency::where('code', $request->primary_currency)->first();
        $secondaryCurrency = Currency::where('code', $request->secondary_currency)->first();
        $customerAccount = Account::find($request->account_id);
        $vaultFrom = Account::find($request->vault_from_id);
        $vaultTo = Account::find($request->vault_to_id);
        $today = now();

        if ($request->type === 'buy') {
            // WE BUY Primary (USD) from Customer using Secondary (IQD)
            
            // 1. We receive Primary into our Vault (Debit Vault)
            JournalService::record($transaction, $vaultTo->id, $primaryCurrency->id, $request->primary_amount, 0, "کڕینی {$request->primary_currency} - وەرگیرا لە {$request->client_name}", $today);
            
            // 2. Customer gives Primary (Credit Customer)
            JournalService::record($transaction, $customerAccount->id, $primaryCurrency->id, 0, $request->primary_amount, "داپەنی {$request->primary_currency} بۆ ئاڵوگۆڕ", $today);

            // 3. Customer receives Secondary (Debit Customer)
            JournalService::record($transaction, $customerAccount->id, $secondaryCurrency->id, $request->secondary_amount, 0, "وەرگرتنی {$request->secondary_currency} لە ئاڵوگۆڕ", $today);

            // 4. We give Secondary from our Vault (Credit Vault)
            JournalService::record($transaction, $vaultFrom->id, $secondaryCurrency->id, 0, $request->secondary_amount, "فرۆشتنی {$request->secondary_currency} - درا بە {$request->client_name}", $today);

        } else {
            // WE SELL Primary (USD) to Customer receiving Secondary (IQD)
            
            // 1. We receive Secondary into our Vault (Debit Vault)
            JournalService::record($transaction, $vaultTo->id, $secondaryCurrency->id, $request->secondary_amount, 0, "وەرگرتنی {$request->secondary_currency} لە {$request->client_name}", $today);

            // 2. Customer gives Secondary (Credit Customer)
            JournalService::record($transaction, $customerAccount->id, $secondaryCurrency->id, 0, $request->secondary_amount, "دانی {$request->secondary_currency} بۆ ئاڵوگۆڕ", $today);

            // 3. Customer receives Primary (Debit Customer)
            JournalService::record($transaction, $customerAccount->id, $primaryCurrency->id, $request->primary_amount, 0, "وەرگرتنی {$request->primary_currency} لە ئاڵوگۆڕ", $today);

            // 4. We give Primary from our Vault (Credit Vault)
            JournalService::record($transaction, $vaultFrom->id, $primaryCurrency->id, 0, $request->primary_amount, "فرۆشتنی {$request->primary_currency} - درا بە {$request->client_name}", $today);
        }

        // 5. Record Profit if realized
        if ($transaction->profit > 0) {
            $profitAccount = Account::where('type', 'revenue')->orWhere('code', 'LIKE', '4%')->first();
            if ($profitAccount) {
                JournalService::record($transaction, $profitAccount->id, $secondaryCurrency->id, 0, $transaction->profit, "قازانجی ئاڵوگۆڕی پسوڵەی #{$transaction->id}", $today);
            }
        }
    }

    public function getProfitReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // 1. Profit from Revenue Accounts
        $profitByCurrency = \App\Models\JournalEntry::whereBetween('date', [$startDate, $endDate])
            ->whereHas('account', function($q) {
                $q->where('code', 'LIKE', '4%')->orWhere('type', 'revenue');
            })
            ->select('currency_id', DB::raw('SUM(credit - debit) as total_profit'))
            ->groupBy('currency_id')
            ->with('currency')
            ->get();

        // 2. Current Asset Balances (Vaults)
        $assets = Account::where('type', 'vault')
            ->with('summaries.currency')
            ->get()
            ->map(function($account) {
                return [
                    'name' => $account->name,
                    'balances' => $account->summaries->map(function($s) {
                        return [
                            'currency' => $s->currency->code,
                            'balance' => $s->total_debit - $s->total_credit
                        ];
                    })
                ];
            });

        return response()->json([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'profits' => $profitByCurrency,
            'assets' => $assets
        ]);
    }

    public function show($id)
    {
        return response()->json(Transaction::with('account')->findOrFail($id));
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $transaction = Transaction::findOrFail($id);
            $transaction->journalEntries->each->delete();
            $transaction->delete();
            return response()->json(['message' => 'Transaction deleted successfully']);
        });
    }

    private function calculateProfit($request)
    {
        $type = $request->type;
        $primary = $request->primary_currency;
        $secondary = $request->secondary_currency;
        $rate = (float)$request->rate;
        $amount = (float)$request->primary_amount;

        if ($type === 'buy') {
            return 0;
        }

        $lastBuyRate = Transaction::where('account_id', $request->account_id)
            ->where('type', 'buy')
            ->where('primary_currency', $primary)
            ->where('secondary_currency', $secondary)
            ->latest()
            ->value('rate');

        if ($lastBuyRate) {
            return ($rate - $lastBuyRate) * ($amount / 100);
        }

        return ($amount / 100) * 500;
    }
}
