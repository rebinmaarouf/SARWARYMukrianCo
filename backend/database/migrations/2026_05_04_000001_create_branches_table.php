<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->boolean('is_main')->default(false);
            $table->string('code')->unique();
            $table->timestamps();
        });

        // Insert the initial branches
        DB::table('branches')->insert([
            [
                'name' => 'لقی سەرەکی (خانەقا)',
                'location' => 'سلێمانی - خانەقا',
                'is_main' => true,
                'code' => 'MAIN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'لقی هاتووچۆ',
                'location' => 'سلێمانی - بەڕێوەبەرایەتی هاتووچۆ',
                'is_main' => false,
                'code' => 'TRAFFIC',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
};
