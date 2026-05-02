<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update Password for the Master User
        DB::table('users')
            ->where('email', 'rebin.maaruf@gmail.com')
            ->update([
                'password' => Hash::make('Ton@haty93??'),
            ]);

        // 2. Create Database Trigger to prevent deletion of the master user
        DB::unprepared("
            CREATE TRIGGER prevent_master_user_deletion 
            BEFORE DELETE ON users
            FOR EACH ROW
            BEGIN
                IF OLD.email = 'rebin.maaruf@gmail.com' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This master user cannot be deleted even by DB admins.';
                END IF;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS prevent_master_user_deletion");
    }
};
