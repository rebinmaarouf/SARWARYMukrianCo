<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of accounts.
     * Supports ?search=13 or ?search=نات for instant lookup.
     */
    public function index(Request $request)
    {
        $query = Account::query();

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        return response()->json(
            $query->orderBy('code')->paginate($request->get('per_page', 50))
        );
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
        ]);

        $account = Account::create($validated);

        return response()->json($account, 201);
    }

    public function show(Account $account)
    {
        return response()->json($account->load(['debtorEntries', 'creditorEntries']));
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|unique:accounts,code,' . $account->id,
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'type' => 'required|string|in:vault,customer,expense,equity,revenue,general',
        ]);

        $account->update($validated);

        return response()->json($account);
    }

    /**
     * Remove the specified account.
     */
    public function destroy(Account $account)
    {
        $account->delete();
        return response()->json(null, 204);
    }
}
