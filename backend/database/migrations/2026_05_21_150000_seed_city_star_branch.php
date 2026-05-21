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
        // 1. Insert Branch
        $branch = DB::table('branches')->where('code', 'CITY_STAR')->first();
        if (!$branch) {
            $branchId = DB::table('branches')->insertGetId([
                'name' => 'لقی سیتی ستار (City Star Branch)',
                'location' => 'سلێمانی - سیتی ستار',
                'is_main' => false,
                'code' => 'CITY_STAR',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $branchId = $branch->id;
        }

        // 2. Link Super Admins to the new branch in the branch_user pivot
        $adminUserIds = DB::table('users')
            ->leftJoin('model_has_roles', function($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                     ->where('model_has_roles.model_type', '=', \App\Models\User::class);
            })
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'Super Admin')
            ->orWhereIn('users.email', ['rebin.maaruf@gmail.com', 'rebinmaarouf@gmail.com', 'admin@admin.com'])
            ->pluck('users.id')
            ->unique();

        foreach ($adminUserIds as $userId) {
            DB::table('branch_user')->insertOrIgnore([
                'user_id' => $userId,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Seed parallel accounts for CITY_STAR
        $accounts = [
            [
                'name' => 'صندوقی سیتی ستار',
                'code' => '181',
                'type' => 'vault',
                'branch_id' => $branchId,
                'is_global' => false,
            ],
            [
                'name' => 'داهاتی عمولەی سیتی ستار',
                'code' => '401',
                'type' => 'revenue',
                'branch_id' => $branchId,
                'is_global' => false,
            ],
            [
                'name' => 'مەسرووفاتی سیتی ستار',
                'code' => '04',
                'type' => 'expense',
                'branch_id' => $branchId,
                'is_global' => false,
            ],
            [
                'name' => 'خێرو زەرەری سیتی ستار',
                'code' => '02',
                'type' => 'equity',
                'branch_id' => $branchId,
                'is_global' => false,
            ],
            [
                'name' => 'قازانجی ئاڵوگۆڕی سیتی ستار',
                'code' => '484',
                'type' => 'revenue',
                'branch_id' => $branchId,
                'is_global' => false,
            ],
            [
                'name' => 'زیانی ئاڵوگۆڕی سیتی ستار',
                'code' => '384',
                'type' => 'expense',
                'branch_id' => $branchId,
                'is_global' => false,
            ],
        ];

        foreach ($accounts as $acc) {
            $exists = DB::table('accounts')
                ->where('name', $acc['name'])
                ->where('branch_id', $acc['branch_id'])
                ->exists();

            if (!$exists) {
                DB::table('accounts')->insert([
                    'name' => $acc['name'],
                    'code' => $acc['code'],
                    'type' => $acc['type'],
                    'branch_id' => $acc['branch_id'],
                    'is_global' => $acc['is_global'],
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
        $branch = DB::table('branches')->where('code', 'CITY_STAR')->first();
        if ($branch) {
            DB::table('accounts')->where('branch_id', $branch->id)->delete();
            DB::table('branch_user')->where('branch_id', $branch->id)->delete();
            DB::table('branches')->where('id', $branch->id)->delete();
        }
    }
};
