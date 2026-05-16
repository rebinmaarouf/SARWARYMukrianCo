<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->enum('type', ['receipt', 'payment']); // receipt (قەبز), payment (سەرف)
            $table->decimal('amount', 20, 4);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('account_id')->constrained('accounts'); // The Client/Expense/Revenue
            $table->foreignId('vault_id')->constrained('accounts'); // The Vault
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('user_id')->constrained('users');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
