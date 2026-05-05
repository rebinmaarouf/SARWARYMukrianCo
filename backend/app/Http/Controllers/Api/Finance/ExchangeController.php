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

                // 1. Calculate Real Value vs Transaction Price (Profit Recognition)
                // System Price in Secondary Currency = (Primary System Rate / Secondary System Rate) * Amount
                $systemRateForPrimary = $primaryCurrency->current_rate;
                $systemRateForSecondary = $secondaryCurrency->current_rate;
                $systemValueInSecondary = ($request->primary_amount * $systemRateForPrimary) / $systemRateForSecondary;

                $transactionValueInSecondary = $request->secondary_amount;
                
                // Profit calculation: 
                // If BUY: Profit = SystemValue - PaidPrice (Buy low = Profit)
                // If SELL: Profit = ReceivedPrice - SystemValue (Sell high = Profit)
                if ($request->type === 'buy') {
                    $profitAmount = $systemValueInSecondary - $transactionValueInSecondary;
                } else {
                    $profitAmount = $transactionValueInSecondary - $systemValueInSecondary;
                }

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
                    'profit' => $profitAmount,
                    'client_name' => $request->client_name,
                    'note' => $request->note,
                    'vault_from_id' => $request->vault_from_id,
                    'vault_to_id' => $request->vault_to_id,
                    'branch_id' => auth()->user()->branch_id ?? 1
                ]);

                // 3. Journal Entries Logic (Scientific IUAS Multi-Leg)
                $today = now();
                
                // Leg 1: Give money (From Vault)
                JournalService::record($transaction, $vaultFrom->id, $secondaryCurrency->id, 0, $request->secondary_amount, "دانی {$request->secondary_currency} بۆ کڕینی {$request->primary_currency}", $today);
                
                // Leg 2: Receive money (To Vault)
                JournalService::record($transaction, $vaultTo->id, $primaryCurrency->id, $request->primary_amount, 0, "وەرگرتنی {$request->primary_currency} (#{$transaction->id})", $today);

                // Leg 3: Book Profit to IUAS 484 (Exchange Revenue)
                if ($profitAmount != 0) {
                    $profitAccount = Account::where('code', '484')->first() ?: Account::where('code', '41')->first();
                    if ($profitAccount) {
                        // Profit is always recorded in the secondary currency (the common denominator)
                        if ($profitAmount > 0) {
                            JournalService::record($transaction, $profitAccount->id, $secondaryCurrency->id, 0, $profitAmount, "قازانجی ئاڵوگۆڕی دراو #{$transaction->id}", $today);
                        } else {
                            // Loss (recorded as Debit in revenue or specific loss account)
                            JournalService::record($transaction, $profitAccount->id, $secondaryCurrency->id, abs($profitAmount), 0, "زیانی ئاڵوگۆڕی دراو #{$transaction->id}", $today);
                        }
                    }
                }

                // Leg 4: Audit trail for Customer/Account if separate from vaults
                if ($request->account_id != $vaultFrom->id && $request->account_id != $vaultTo->id) {
                    $customerAccount = Account::find($request->account_id);
                    // In the customer's statement, show the swap
                    JournalService::record($transaction, $customerAccount->id, $secondaryCurrency->id, $request->secondary_amount, 0, "وەرگرتنەوەی {$request->secondary_currency}", $today);
                    JournalService::record($transaction, $customerAccount->id, $primaryCurrency->id, 0, $request->primary_amount, "ڕادەستکردنی {$request->primary_currency}", $today);
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
        $customerAccount = Account::find($request->account_id);
        $today = now();

        $profitAmount = (float) $transaction->profit;

        if ($request->type === 'buy') {
            // WE BUY USD (Primary) / WE GIVE IQD (Secondary)
            
            // Leg 1: Debit the Destination (Where USD goes)
            JournalService::record($transaction, $vaultTo->id, $primaryCurrency->id, $request->primary_amount, 0, "وەرگرتنی {$request->primary_currency} (#{$transaction->id})", $today);
            
            // Leg 2: Credit the Source (Where IQD comes from/stays)
            // If vaultFrom is a customer, it increases our liability to them (Credit)
            JournalService::record($transaction, $vaultFrom->id, $secondaryCurrency->id, 0, $request->secondary_amount, "دانی {$request->secondary_currency} بۆ ئاڵوگۆڕ", $today);

            // Audit: Link to customer statement if they aren't the vaults
            if ($customerAccount->id !== $vaultTo->id && $customerAccount->id !== $vaultFrom->id) {
                // This creates the audit trail in the customer's ledger without moving cash twice
                // In a Buy trade, the customer "Gives Primary" and "Receives Secondary"
                JournalService::record($transaction, $customerAccount->id, $primaryCurrency->id, 0, $request->primary_amount, "ڕادەستکردنی دۆلار", $today);
                JournalService::record($transaction, $customerAccount->id, $secondaryCurrency->id, $request->secondary_amount, 0, "وەرگرتنی دینار", $today);
            }
        } else {
            // WE SELL USD (Primary) / WE RECEIVE IQD (Secondary)
            $netSecondary = $request->secondary_amount - $profitAmount;

            // Leg 1: Debit the Destination (Where IQD goes)
            JournalService::record($transaction, $vaultTo->id, $secondaryCurrency->id, $request->secondary_amount, 0, "وەرگرتنی {$request->secondary_currency} (#{$transaction->id})", $today);

            // Leg 2: Credit the Source (Where USD comes from)
            JournalService::record($transaction, $vaultFrom->id, $primaryCurrency->id, 0, $request->primary_amount, "دانی {$request->primary_currency} (#{$transaction->id})", $today);

            // Leg 3: Move Profit from Destination to Profit Account
            // This ensures the routing account (Natron/Vault) only keeps the principal
            $profitAccount = Account::where('code', '02')->first() ?: Account::where('code', '401')->first();
            if ($profitAccount && $profitAmount > 0) {
                // Debit the VaultTo (take profit out)
                JournalService::record($transaction, $vaultTo->id, $secondaryCurrency->id, 0, $profitAmount, "گواستنەوەی قازانج بۆ حیسابی ٠٢", $today);
                // Credit the Profit Account
                JournalService::record($transaction, $profitAccount->id, $secondaryCurrency->id, 0, $profitAmount, "قازانجی ئاڵوگۆڕ #{$transaction->id}", $today);
            }

            // Audit for customer
            if ($customerAccount->id !== $vaultTo->id && $customerAccount->id !== $vaultFrom->id) {
                JournalService::record($transaction, $customerAccount->id, $secondaryCurrency->id, 0, $netSecondary, "دانی دینار", $today);
                JournalService::record($transaction, $customerAccount->id, $primaryCurrency->id, $request->primary_amount, 0, "وەرگرتنی دۆلار", $today);
            }
        }
    }

    public function getProfitReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // 1. Profit from Revenue Accounts
        $profitByCurrency = \App\Models\JournalEntry::whereBetween('date', [$startDate, $endDate])
            ->whereHas('account', function ($q) {
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
