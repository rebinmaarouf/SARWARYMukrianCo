<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            }
        });

        // Set existing logs to main branch
        DB::table('activity_logs')->whereNull('branch_id')->update(['branch_id' => 1]);
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
