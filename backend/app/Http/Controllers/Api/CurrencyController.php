<?php

namespace App\Http\Controllers\Api;

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

    /**
     * Remove the specified currency.
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();
        return response()->json(null, 204);
    }
}
