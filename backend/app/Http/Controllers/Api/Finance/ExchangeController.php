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
        $query = Transaction::with(['account', 'user', 'vault_from', 'vault_to'])->latest();

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        return response()->json($query->paginate(50));
    }

    private function getMultiplier($currencyCode)
    {
        if ($currencyCode === 'IQD') {
            return 1.0;
        }
        if ($currencyCode === 'IRR') {
            return 0.0000001; // Toman multiplier (10,000,000 unit block)
        }
        return 0.01; // Block multiplier (100 unit block for USD, GBP, EUR, TRY, etc.)
    }

    private function getMovingAverageCost($primaryCurrency, $limitToId = null)
    {
        $query = Transaction::where('primary_currency', $primaryCurrency)
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc');

        if ($limitToId) {
            $query->where('id', '<', $limitToId);
        }

        $txs = $query->get();

        $runningQty = 0.0;
        $runningCost = 0.0;
        $currentWac = 0.0;

        foreach ($txs as $tx) {
            if ($tx->type === 'buy') {
                $runningQty += (float)$tx->primary_amount;
                $runningCost += (float)$tx->secondary_amount;
                if ($runningQty > 0) {
                    $currentWac = $runningCost / $runningQty;
                }
            } else { // sell
                $soldQty = (float)$tx->primary_amount;
                $costOfSold = $soldQty * $currentWac;
                $runningQty -= $soldQty;
                $runningCost -= $costOfSold;
                if ($runningQty <= 0) {
                    $runningQty = 0.0;
                    $runningCost = 0.0;
                    $currentWac = 0.0;
                }
            }
        }

        return $currentWac;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'account_id' => 'nullable|exists:accounts,id',
                'type' => 'required|in:buy,sell',
                'pair' => 'required|string',
                'primary_currency' => 'required|string',
                'primary_amount' => 'required|numeric',
                'secondary_currency' => 'required|string',
                'secondary_amount' => 'required|numeric',
                'rate' => 'required|numeric',
                'vault_from_id' => 'required|exists:accounts,id',
                'vault_to_id' => 'required|exists:accounts,id',
                'note' => 'nullable|string',
                'client_name' => 'nullable|string'
            ]);

            return DB::transaction(function () use ($request) {
                $vaultFrom = Account::find($request->vault_from_id);
                $vaultTo = Account::find($request->vault_to_id);
                
                $primaryCurrency = Currency::where('code', $request->primary_currency)->first();
                $secondaryCurrency = Currency::where('code', $request->secondary_currency)->first();

                if ($request->primary_currency === 'IQD' && $request->secondary_currency === 'USD') {
                    // Special case: IQD/USD inverse pair
                    $systemRateOfUsd = $secondaryCurrency->current_rate; // e.g., 1500
                    $systemValueInSecondary = $request->primary_amount / $systemRateOfUsd;
                    $transactionValueInSecondary = $request->secondary_amount;

                    if ($request->type === 'buy') {
                        // WE BUY IQD / WE GIVE USD (Profit = System value in USD - actual USD we gave)
                        $profitAmount = $systemValueInSecondary - $transactionValueInSecondary;
                    } else {
                        // WE SELL IQD / WE RECEIVE USD (Profit = actual USD we received - System value in USD)
                        $profitAmount = $transactionValueInSecondary - $systemValueInSecondary;
                    }
                } else {
                    // Standard currency pairs
                    $primaryMultiplier = $this->getMultiplier($request->primary_currency);
                    $unitTransactionRate = (float)$request->rate * $primaryMultiplier;

                    $systemRateForPrimary = $primaryCurrency->current_rate;
                    $systemRateForSecondary = $secondaryCurrency->current_rate;

                    if (!$primaryCurrency->is_base) {
                        if ($request->type === 'buy') {
                            $systemRateForPrimary = $unitTransactionRate;
                        } else { // sell
                            $systemRateForPrimary = $this->getMovingAverageCost($request->primary_currency);
                            if ($systemRateForPrimary <= 0) {
                                $systemRateForPrimary = $unitTransactionRate;
                            }
                        }
                    }

                    if (!$secondaryCurrency->is_base) {
                        $systemRateForSecondary = $unitTransactionRate;
                    }

                    $systemValueInSecondary = ($request->primary_amount * $systemRateForPrimary) / $systemRateForSecondary;
                    $transactionValueInSecondary = $request->secondary_amount;
                    
                    if ($request->type === 'buy') {
                        $profitAmount = $systemValueInSecondary - $transactionValueInSecondary;
                    } else {
                        $profitAmount = $transactionValueInSecondary - $systemValueInSecondary;
                    }
                }

                // 4. Create Transaction record
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
                    'profit' => $profitAmount,
                    'client_name' => $request->client_name,
                    'note' => $request->note,
                    'vault_from_id' => $request->vault_from_id,
                    'vault_to_id' => $request->vault_to_id,
                    'branch_id' => auth()->user()->branch_id ?? 1
                ]);

                // 5. Delegate to comprehensive Journal Service Method
                $this->recordJournalEntries($transaction, $request);

                // Real-time Push Notification trigger
                try {
                    $options = [
                        'cluster' => env('PUSHER_APP_CLUSTER'),
                        'useTLS' => true
                    ];
                    $pusher = new \Pusher\Pusher(
                        env('PUSHER_APP_KEY'),
                        env('PUSHER_APP_SECRET'),
                        env('PUSHER_APP_ID'),
                        $options
                    );

                    $msg = ($transaction->type === 'buy' ? 'کڕینی ' : 'فرۆشتنی ') . 
                           number_format($transaction->primary_amount) . ' ' . $transaction->primary_currency . 
                           ' بە نرخی ' . number_format($transaction->rate) . 
                           ' بۆ حیسابی ' . ($transaction->client_name ?: $transaction->account?->name) . ' ئەنجامدرا.';

                    $pusher->trigger('currency-exchange', 'transaction-created', [
                        'id' => $transaction->id,
                        'title' => $transaction->type === 'buy' ? '💸 کڕینی دراو' : '💰 فرۆشتنی دراو',
                        'message' => $msg,
                        'type' => $transaction->type,
                        'time' => now()->format('H:i')
                    ]);

                    if ($transaction->profit < -1000) {
                        $currencyLabel = ($transaction->secondary_currency === 'USD') ? 'دۆلار' : 'دینار';
                        $pusher->trigger('currency-exchange', 'transaction-created', [
                            'id' => $transaction->id,
                            'title' => '🔴 ئاگاداری زیانی گەورە',
                            'message' => "مامەڵەی #{$transaction->id} بڕی زیانێکی زۆری تۆمارکردووە (" . number_format(abs($transaction->profit)) . " {$currencyLabel})!",
                            'type' => 'anomaly',
                            'time' => now()->format('H:i')
                        ]);
                    }
                } catch (\Exception $pe) {
                    \Log::error('Pusher notification error: ' . $pe->getMessage());
                }

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
        $vaultFrom = Account::find($request->vault_from_id);
        $vaultTo = Account::find($request->vault_to_id);
        $today = now();

        $profitAmount = (float) $transaction->profit;

        if ($request->primary_currency === 'IQD' && $request->secondary_currency === 'USD') {
            // Special Case: IQD/USD inverse pair
            $systemRateOfUsd = $secondaryCurrency->current_rate; // e.g., 1500

            if ($request->type === 'buy') {
                // WE BUY IQD / WE GIVE USD
                // Leg 1: Debit Destination Vault (IQD comes IN)
                JournalService::record($transaction, $vaultTo->id, $primaryCurrency->id, $request->primary_amount, 0, "وەرگرتنی {$request->primary_currency} - {$request->client_name} (#{$transaction->id})", $today, 1.0);
                
                // Leg 2: Credit Source Vault (USD goes OUT)
                JournalService::record($transaction, $vaultFrom->id, $secondaryCurrency->id, 0, $request->secondary_amount, "دانی {$request->secondary_currency} - {$request->client_name} (#{$transaction->id})", $today, $systemRateOfUsd);
            } else {
                // WE SELL IQD / WE RECEIVE USD
                // Leg 1: Debit Destination Vault (USD comes IN)
                JournalService::record($transaction, $vaultTo->id, $secondaryCurrency->id, $request->secondary_amount, 0, "وەرگرتنی {$request->secondary_currency} - {$request->client_name} (#{$transaction->id})", $today, $systemRateOfUsd);

                // Leg 2: Credit Source Vault (IQD goes OUT)
                JournalService::record($transaction, $vaultFrom->id, $primaryCurrency->id, 0, $request->primary_amount, "دانی {$request->primary_currency} - {$request->client_name} (#{$transaction->id})", $today, 1.0);
            }
        } else {
            // Standard currency pairs
            $primaryMultiplier = $this->getMultiplier($request->primary_currency);
            $unitTransactionRate = (float)$request->rate * $primaryMultiplier;

            // Determine System Unit Rate for primary currency valuation in ledger
            $systemRateForPrimary = $primaryCurrency->current_rate;
            if (!$primaryCurrency->is_base) {
                if ($request->type === 'buy') {
                    $systemRateForPrimary = $unitTransactionRate;
                } else { // sell
                    $systemRateForPrimary = $this->getMovingAverageCost($request->primary_currency, $transaction->id);
                    if ($systemRateForPrimary <= 0) {
                        $systemRateForPrimary = $unitTransactionRate;
                    }
                }
            }

            if ($request->type === 'buy') {
                // WE BUY Primary / WE GIVE Secondary
                JournalService::record($transaction, $vaultTo->id, $primaryCurrency->id, $request->primary_amount, 0, "وەرگرتنی {$request->primary_currency} - {$request->client_name} (#{$transaction->id})", $today, $systemRateForPrimary);
                JournalService::record($transaction, $vaultFrom->id, $secondaryCurrency->id, 0, $request->secondary_amount, "دانی {$request->secondary_currency} - {$request->client_name} (#{$transaction->id})", $today);
            } else {
                // WE SELL Primary / WE RECEIVE Secondary
                JournalService::record($transaction, $vaultTo->id, $secondaryCurrency->id, $request->secondary_amount, 0, "وەرگرتنی {$request->secondary_currency} - {$request->client_name} (#{$transaction->id})", $today);
                JournalService::record($transaction, $vaultFrom->id, $primaryCurrency->id, 0, $request->primary_amount, "دانی {$request->primary_currency} - {$request->client_name} (#{$transaction->id})", $today, $systemRateForPrimary);
            }
        }

        // Leg 3: Book Profit to IUAS 484 (Revenue) or 384 (Expense/Loss)
        if ($profitAmount != 0) {
            if ($profitAmount > 0) {
                // Gain goes to 484 (Revenue)
                $profitAccount = Account::where('code', '484')->first() ?: Account::where('code', '41')->first();
                if ($profitAccount) {
                    JournalService::record($transaction, $profitAccount->id, $secondaryCurrency->id, 0, $profitAmount, "قازانجی ئاڵوگۆڕ #{$transaction->id}", $today);
                }
            } else {
                // Loss goes to 384 (Expense/Loss)
                // Proactively find or create 384 according to Iraqi Unified Accounting System!
                $lossAccount = Account::where('code', '384')->first();
                if (!$lossAccount) {
                    $lossAccount = Account::create([
                        'code' => '384',
                        'name' => 'زیانی ئاڵوگۆڕی دراو',
                        'type' => 'expense',
                        'branch_id' => $transaction->branch_id ?? 1
                    ]);
                }
                JournalService::record($transaction, $lossAccount->id, $secondaryCurrency->id, abs($profitAmount), 0, "زیانی ئاڵوگۆڕ #{$transaction->id}", $today);
            }
        }
    }

    public function getProfitReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // 1. Profit from Revenue Accounts (484/4xx) and Loss from Expense Accounts (384/3xx)
        $profitByCurrency = \App\Models\JournalEntry::whereBetween('date', [$startDate, $endDate])
            ->whereHas('account', function ($q) {
                $q->where('code', '484')
                  ->orWhere('code', '384')
                  ->orWhere('code', 'LIKE', '4%')
                  ->orWhere('type', 'revenue');
            })
            ->select('currency_id', DB::raw('SUM(credit - debit) as total_profit'))
            ->groupBy('currency_id')
            ->with('currency')
            ->get();

        // 2. Current Asset Balances (Vaults)
        $assets = Account::where('type', 'vault')
            ->with('summaries.currency')
            ->get()
            ->map(function ($account) {
                return [
                    'name' => $account->name,
                    'balances' => $account->summaries->map(function ($s) {
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
        return response()->json(Transaction::with(['account', 'user', 'vault_from', 'vault_to'])->findOrFail($id));
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('delete journals')) {
            abort(403, 'Unauthorized action. You do not have permission to delete journals.');
        }

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
        $rate = (float) $request->rate;
        $amount = (float) $request->primary_amount;

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
