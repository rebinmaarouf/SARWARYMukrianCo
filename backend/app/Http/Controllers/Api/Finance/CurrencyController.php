<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display all currencies (for dynamic form generation).
     */
    public function index()
    {
        return response()->json(Currency::all());
    }

    /**
     * Store a newly created currency.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:currencies,code',
            'symbol' => 'required|string|max:10',
            'is_base' => 'boolean',
        ]);

        $currency = Currency::create($validated);
        return response()->json($currency, 201);
    }

    /**
     * Display the specified currency.
     */
    public function show(Currency $currency)
    {
        return response()->json($currency);
    }

    /**
     * Update the specified currency.
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:currencies,code,' . $currency->id,
            'symbol' => 'required|string|max:10',
            'is_base' => 'boolean',
        ]);

        $currency->update($validated);
        return response()->json($currency);
    }

    public function updateRate(Request $request)
    {
        $validated = $request->validate([
            'to' => 'required|string|exists:currencies,code',
            'rate' => 'required|numeric|min:0.0000001',
        ]);

        $currency = Currency::where('code', $validated['to'])->firstOrFail();

        if ($currency->is_base) {
            // If they are updating the base currency (IQD), they are updating the price of 1 USD in IQD.
            $usd = Currency::where('code', 'USD')->first();
            if ($usd) {
                \App\Models\ExchangeRate::updateOrCreate(
                    ['currency_id' => $usd->id, 'date' => now()->format('Y-m-d')],
                    ['rate' => $validated['rate']]
                );
                $currency = $usd;
            }
        } else {
            // Update rate for non-base currencies (GBP, EUR, TRY, IRR, etc.)
            \App\Models\ExchangeRate::updateOrCreate(
                ['currency_id' => $currency->id, 'date' => now()->format('Y-m-d')],
                ['rate' => $validated['rate']]
            );
        }

        return response()->json([
            'message' => 'Exchange rate updated successfully',
            'currency' => $currency->fresh()
        ]);
    }

    /**
     * Remove the specified currency.
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();
        return response()->json(null, 204);
    }
}
