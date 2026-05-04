<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'users',
            'accounts',
            'account_summaries',
            'journal_entries',
            'transactions',
            'registry_entries'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
                }
            });

            // Update existing records to the main branch (ID: 1)
            DB::table($tableName)->whereNull('branch_id')->update(['branch_id' => 1]);
        }
    }

    public function down()
    {
        $tables = [
            'users',
            'accounts',
            'account_summaries',
            'journal_entries',
            'transactions',
            'registry_entries'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
