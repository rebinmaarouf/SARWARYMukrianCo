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
        Schema::table('transfers', function (Blueprint $table) {
            $table->decimal('commission_amount', 20, 2)->default(0)->after('amount');
            $table->foreignId('commission_currency_id')->nullable()->after('commission_amount')->constrained('currencies');
            $table->foreignId('commission_account_id')->nullable()->after('commission_currency_id')->constrained('accounts');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeign(['commission_currency_id']);
            $table->dropForeign(['commission_account_id']);
            $table->dropColumn(['commission_amount', 'commission_currency_id', 'commission_account_id']);
        });
    }
};
