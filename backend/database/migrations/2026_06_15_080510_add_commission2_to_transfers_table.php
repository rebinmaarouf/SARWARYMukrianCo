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
            $table->decimal('commission_amount_2', 20, 2)->default(0)->after('commission_amount');
            $table->foreignId('commission_currency_2_id')->nullable()->after('commission_amount_2')->constrained('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn(['commission_amount_2', 'commission_currency_2_id']);
        });
    }
};
