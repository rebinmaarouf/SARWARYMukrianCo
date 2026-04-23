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
        Schema::create('remittances', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'incoming', 'outgoing'
            $table->string('sender_name');
            $table->string('receiver_name');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('amount', 20, 2);
            $table->decimal('commission', 15, 2)->default(0);
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remittances');
    }
};
