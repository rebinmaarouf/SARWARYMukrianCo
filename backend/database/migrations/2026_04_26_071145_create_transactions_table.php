<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained(); // Who made the transaction
            $blueprint->foreignId('account_id')->constrained(); // The account involved (Vault/Customer)
            
            $blueprint->string('type'); // buy or sell
            $blueprint->string('pair'); // e.g., USD/IQD, USD/IRR
            
            // Primary Currency (The one being bought or sold)
            $blueprint->string('primary_currency'); // e.g., USD
            $blueprint->decimal('primary_amount', 20, 4);
            
            // Secondary Currency (The payment currency)
            $blueprint->string('secondary_currency'); // e.g., IQD
            $blueprint->decimal('secondary_amount', 20, 4);
            
            $blueprint->decimal('rate', 20, 4); // Exchange rate used
            
            $blueprint->decimal('profit', 20, 4)->default(0); // Calculated profit from this deal
            
            $blueprint->string('client_name')->nullable(); // For temporary clients without accounts
            $blueprint->text('note')->nullable();
            
            $blueprint->timestamps();
            $blueprint->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
