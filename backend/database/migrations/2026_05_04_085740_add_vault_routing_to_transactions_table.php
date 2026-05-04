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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('vault_from_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignId('vault_to_id')->nullable()->constrained('accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['vault_from_id']);
            $table->dropForeign(['vault_to_id']);
            $table->dropColumn(['vault_from_id', 'vault_to_id']);
        });
    }
};
