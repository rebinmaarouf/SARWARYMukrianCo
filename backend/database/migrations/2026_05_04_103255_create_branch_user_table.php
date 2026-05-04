<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branch_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'branch_id']);
        });

        // Migration logic: Move current user's branch_id to this pivot table
        $users = \App\Models\User::whereNotNull('branch_id')->get();
        foreach ($users as $user) {
            \Illuminate\Support\Facades\DB::table('branch_user')->insert([
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('branch_user');
    }
};
