<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Seed Capital for City Star (Branch ID 3)
        $cityStarBranch = DB::table('branches')->where('code', 'CITY_STAR')->first();
        if ($cityStarBranch) {
            $exists = DB::table('accounts')
                ->where('name', 'رأس مال سیتی ستار')
                ->where('branch_id', $cityStarBranch->id)
                ->exists();

            if (!$exists) {
                DB::table('accounts')->insert([
                    'name' => 'رأس مال سیتی ستار',
                    'code' => '211',
                    'type' => 'equity',
                    'branch_id' => $cityStarBranch->id,
                    'is_global' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Seed Capital for Boleen (Branch ID 4)
        $boleenBranch = DB::table('branches')->where('code', 'BOLEEN')->first();
        if ($boleenBranch) {
            $exists = DB::table('accounts')
                ->where('name', 'رأس مال بۆڵین')
                ->where('branch_id', $boleenBranch->id)
                ->exists();

            if (!$exists) {
                DB::table('accounts')->insert([
                    'name' => 'رأس مال بۆڵین',
                    'code' => '211',
                    'type' => 'equity',
                    'branch_id' => $boleenBranch->id,
                    'is_global' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $cityStarBranch = DB::table('branches')->where('code', 'CITY_STAR')->first();
        if ($cityStarBranch) {
            DB::table('accounts')->where('name', 'رأس مال سیتی ستار')->where('branch_id', $cityStarBranch->id)->delete();
        }

        $boleenBranch = DB::table('branches')->where('code', 'BOLEEN')->first();
        if ($boleenBranch) {
            DB::table('accounts')->where('name', 'رأس مال بۆڵین')->where('branch_id', $boleenBranch->id)->delete();
        }
    }
};
