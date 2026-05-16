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
            ->whereIn('account_id', $accountIds->merge(Account::whereIn('parent_id', $accountIds)->pluck('id')))
            ->whereNull('deleted_at')
            ->select('account_id', 'currency_id', DB::raw('SUM(debit - credit) as balance'))
            ->groupBy('account_id', 'currency_id')
            ->get();

        $accounts->getCollection()->transform(function ($account) use ($balances) {
            // Get children IDs if any
            $childIds = Account::where('parent_id', $account->id)->pluck('id');
            $relevantIds = $childIds->push($account->id);

            $accountBalances = $balances->whereIn('account_id', $relevantIds)
                ->groupBy('currency_id')
                ->map(function ($group, $currId) {
                    $currency = \App\Models\Currency::find($currId);
                    return [
                        'currency_code' => $currency->code ?? '???',
                        'symbol' => $currency->symbol ?? '',
                        'amount' => (float)$group->sum('balance')
                    ];
                })->values();
            
            $account->balances = $accountBalances;
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
            'name' => 'required|string|max:255|unique:accounts,name',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'type' => 'required|string|in:vault,client,expense,equity,revenue,general',
            'parent_id' => 'nullable|exists:accounts,id',
            'branch_id' => 'nullable|integer',
            'is_global' => 'nullable|boolean'
        ]);

        $account = Account::create($validated);

        return response()->json($account, 201);
    }

    public function show(Account $account)
    {
        $account->load(['parent', 'children', 'summaries.currency']);
        
        // Scientific Consolidation: If it's a parent, sum up all children summaries
        if ($account->children->isNotEmpty()) {
            $childIds = $account->children->pluck('id');
            $consolidated = \App\Models\AccountSummary::whereIn('account_id', $childIds->push($account->id))
                ->select('currency_id', 
                    DB::raw('SUM(total_debit) as total_debit'),
                    DB::raw('SUM(total_credit) as total_credit')
                )
                ->groupBy('currency_id')
                ->with('currency')
                ->get();
            
            // Temporary replacement for the view
            $account->setRelation('summaries', $consolidated);
        }

        return response()->json($account);
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255|unique:accounts,name,' . $account->id,
            'type' => 'nullable|in:vault,client,revenue,expense,equity',
            'code' => 'nullable|unique:accounts,code,' . $account->id,
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|integer',
            'is_global' => 'nullable|boolean'
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
        return DB::transaction(function () {
            // Clear current summaries
            \App\Models\AccountSummary::truncate();

            // Recalculate everything from Journal Entries
            $balances = \App\Models\JournalEntry::select(
                    'account_id', 
                    'currency_id', 
                    DB::raw('SUM(debit) as sum_debit'),
                    DB::raw('SUM(credit) as sum_credit')
                )
                ->groupBy('account_id', 'currency_id')
                ->get();

            foreach ($balances as $b) {
                \App\Models\AccountSummary::create([
                    'account_id' => $b->account_id,
                    'currency_id' => $b->currency_id,
                    'total_debit' => $b->sum_debit,
                    'total_credit' => $b->sum_credit,
                ]);
            }

            return response()->json(['message' => 'Balances recalculated successfully']);
        });
    }
}
