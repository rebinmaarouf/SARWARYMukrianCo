<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Currency;
use App\Models\Account;
use App\Services\JournalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::with(['account', 'vault', 'currency', 'user'])
            ->orderBy('id', 'desc');

        // Apply Branch Scope manually if user has branch_id and no global permission
        if (auth()->check() && auth()->user()->branch_id && !auth()->user()->hasRole('super-admin')) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        if ($request->has('type') && in_array($request->type, ['receipt', 'payment'])) {
            $query->where('type', $request->type);
        }

        return response()->json($query->paginate(50));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:receipt,payment',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'account_id' => 'required|exists:accounts,id',
            'vault_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:date',
            'branch_id' => 'nullable|exists:branches,id'
        ]);

        $currency = Currency::findOrFail($request->currency_id);
        $vault = Account::findOrFail($request->vault_id);
        
        // Ensure vault is actually a vault type account
        if ($vault->type !== 'vault') {
            throw ValidationException::withMessages(['vault_id' => 'ئەم حیسابە قاسە نییە.']);
        }

        return DB::transaction(function () use ($request, $currency) {
            // Generate Voucher Number
            $prefix = $request->type === 'receipt' ? 'RV-' : 'PV-';
            $today = date('Y-m-d');
            $countToday = Voucher::withTrashed()
                ->where('type', $request->type)
                ->whereDate('created_at', $today)
                ->count();
            $nextSeq = $countToday + 1;
            $voucherNumber = $prefix . date('ymd') . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            // Determine branch
            $branchId = $request->branch_id ?? auth()->user()->branch_id;
            if (!$branchId) {
                // Fallback to primary branch
                $branchId = 1;
            }

            $voucher = Voucher::create([
                'voucher_number' => $voucherNumber,
                'type' => $request->type,
                'amount' => $request->amount,
                'currency_id' => $request->currency_id,
                'account_id' => $request->account_id,
                'vault_id' => $request->vault_id,
                'branch_id' => $branchId,
                'user_id' => auth()->id(),
                'date' => $request->date,
                'due_date' => $request->due_date,
                'notes' => $request->notes
            ]);

            $systemRate = $currency->current_rate; // Current official rate to evaluate IQD equivalent
            
            $description = $request->type === 'receipt' 
                ? "وەسڵی وەرگرتن (قەبز) #{$voucherNumber} - {$request->notes}"
                : "وەسڵی خەرجکردن (سەرف) #{$voucherNumber} - {$request->notes}";

            if ($request->type === 'receipt') {
                // Receipt (قەبز)
                // Debit: Vault
                JournalService::record($voucher, $voucher->vault_id, $currency->id, $voucher->amount, 0, $description, $voucher->date, $systemRate, $branchId);
                // Credit: Client/Revenue
                JournalService::record($voucher, $voucher->account_id, $currency->id, 0, $voucher->amount, $description, $voucher->date, $systemRate, $branchId);
            } else {
                // Payment (سەرف)
                // Debit: Client/Expense
                JournalService::record($voucher, $voucher->account_id, $currency->id, $voucher->amount, 0, $description, $voucher->date, $systemRate, $branchId);
                // Credit: Vault
                JournalService::record($voucher, $voucher->vault_id, $currency->id, 0, $voucher->amount, $description, $voucher->date, $systemRate, $branchId);
            }

            return response()->json($voucher->load(['account', 'vault', 'currency', 'user']));
        });
    }

    public function destroy(Voucher $voucher)
    {
        return DB::transaction(function () use ($voucher) {
            // Delete associated journal entries
            $voucher->journalEntries()->delete();
            $voucher->delete();
            return response()->json(['message' => 'Voucher deleted successfully']);
        });
    }
}
