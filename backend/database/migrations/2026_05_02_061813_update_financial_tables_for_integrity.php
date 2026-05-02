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
        // 1. Update Transfers table
        Schema::table('transfers', function (Blueprint $table) {
            $table->decimal('amount', 20, 4)->change();
            $table->softDeletes();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->onDelete('set null');
        });

        // 2. Add SoftDeletes to Journal Entries
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 3. Add SoftDeletes to Accounts
        Schema::table('accounts', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 4. Add SoftDeletes to Registry Entries
        Schema::table('registry_entries', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
