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
        Schema::create('registry_entries', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            
            // For general registries, we might map to specific ledgers
            $table->foreignId('currency_id')->constrained();
            $table->decimal('amount', 20, 2); // دینار / دولار
            
            // The two sides of the transaction
            $table->foreignId('debtor_account_id')->nullable()->constrained('accounts'); // قەرزدارە. مدین
            $table->foreignId('creditor_account_id')->nullable()->constrained('accounts'); // لامانە. دائن
            
            $table->decimal('commission_1', 20, 2)->default(0); // عموله 1
            $table->decimal('commission_2', 20, 2)->default(0); // عموله 2
            
            $table->string('sender')->nullable(); // نێردر
            $table->string('receiver')->nullable(); // وەرگر
            $table->text('notes')->nullable(); // ملاحظة
            
            $table->foreignId('user_id')->constrained(); // The user who made the entry
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registry_entries');
    }
};
