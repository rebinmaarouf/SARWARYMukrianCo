<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Currency;
use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $owner = User::firstOrCreate(
            ['email' => 'rebinmaarouf@gmail.com'],
            [
                'name' => 'Rebin Maarouf',
                'password' => Hash::make('password123'),
            ]
        );
        $owner->assignRole('Super Admin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Super Admin');

        // Seed Currencies
        Currency::firstOrCreate(['code' => 'IQD'], ['name' => 'دیناری عێراقی', 'symbol' => 'IQD', 'is_base' => true]);
        Currency::firstOrCreate(['code' => 'USD'], ['name' => 'دۆلاری ئەمریکی', 'symbol' => '$', 'is_base' => false]);

        // Seed Default Accounts
        Account::firstOrCreate(['name' => 'صندوقی عام'], ['code' => '01', 'type' => 'vault']);
        Account::firstOrCreate(['name' => 'خێر و زەرەر'], ['code' => '02', 'type' => 'equity']);
        Account::firstOrCreate(['name' => 'راس المال'], ['code' => '03', 'type' => 'equity']);
        Account::firstOrCreate(['name' => 'مەسروفات عام'], ['code' => '04', 'type' => 'expense']);
        Account::firstOrCreate(['name' => 'کۆمپانیای ناترۆن'], ['code' => '13', 'type' => 'customer']);
    }
}
