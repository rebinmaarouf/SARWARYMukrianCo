<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Display a listing of accounts.
     * Supports ?search=13 or ?search=نات for instant lookup.
     */
    public function index(Request $request)
    {
        $query = Account::with('parent');

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($request->has('roots_only')) {
            $query->whereNull('parent_id');
        }

        // Calculate Balances grouped by Currency for each account
        $accounts = $query->orderBy('code')->paginate($request->input('per_page', 50));
        
        $accountIds = $accounts->pluck('id');
        
        // Fetch sums grouped by account and currency
        $balances = DB::table('journal_entries')
            ->whereIn('account_id', $accountIds)
            ->select('account_id', 'currency_id', DB::raw('SUM(debit - credit) as balance'))
            ->groupBy('account_id', 'currency_id')
            ->get()
            ->groupBy('account_id');

        $accounts->getCollection()->transform(function ($account) use ($balances) {
            $accountBalances = $balances->get($account->id) ?? collect();
            
            // Map balances to currency symbols/codes
            $account->balances = $accountBalances->map(function($b) {
                $currency = \App\Models\Currency::find($b->currency_id);
                return [
                    'currency_code' => $currency->code ?? '???',
                    'symbol' => $currency->symbol ?? '',
                    'amount' => (float)$b->balance
                ];
            });
            
            return $account;
        });

        return response()->json($accounts);
    }

    /**
     * Store a newly created account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|unique:accounts,code',
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'type' => 'required|string|in:vault,customer,expense,equity,revenue,general',
            'parent_id' => 'nullable|exists:accounts,id'
        ]);

        $account = Account::create($validated);

        return response()->json($account, 201);
    }

    public function show(Account $account)
    {
        return response()->json($account->load(['parent', 'children', 'summaries.currency']));
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|in:vault,client,revenue,expense,equity',
            'code' => 'nullable|unique:accounts,code,' . $account->id,
            'notes' => 'nullable|string',
        ]);

        $account->update(array_filter($validated));
        return response()->json($account);
    }

    /**
     * Get the full account hierarchy.
     */
    public function getHierarchy()
    {
        $accounts = Account::whereNull('parent_id')
            ->with('children.children')
            ->orderBy('code')
            ->get();
            
        return response()->json($accounts);
    }

    /**
     * Remove the specified account.
     */
    public function destroy(Account $account)
    {
        // Safety check: Don't delete accounts with journal entries
        if ($account->journalEntries()->exists()) {
            return response()->json(['error' => 'ناتوانیت ئەم حسابە بسڕیتەوە چونکە مێژووی مەعامەلاتی هەیە. باشترە ناوی بگۆڕیت.'], 422);
        }

        $account->delete();
        return response()->json(['message' => 'حسابەکە بە سەرکەوتوویی سڕایەوە']);
    }

    public function recalculateBalances()
    {
        return \Illuminate\Support\Facades\DB::transaction(function () {
            // Clear current summaries
            \App\Models\AccountSummary::truncate();

            // Recalculate everything from Journal Entries
            $balances = \App\Models\JournalEntry::select('account_id', 'currency_id', \Illuminate\Support\Facades\DB::raw('SUM(debit - credit) as total_balance'))
                ->groupBy('account_id', 'currency_id')
                ->get();

            foreach ($balances as $b) {
                \App\Models\AccountSummary::create([
                    'account_id' => $b->account_id,
                    'currency_id' => $b->currency_id,
                    'balance' => $b->total_balance,
                ]);
            }

            return response()->json(['message' => 'Balances recalculated successfully']);
        });
    }
}
