<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->string('hash', 64)->nullable()->after('branch_id');
            $table->string('previous_hash', 64)->nullable()->after('hash');
            
            $table->index(['hash']);
        });

        // Sequentially populate cryptographic hash chains for all existing entries
        $entries = DB::table('journal_entries')
            ->orderBy('id', 'asc')
            ->get();

        $previousHash = "0000000000000000000000000000000000000000000000000000000000000000";

        foreach ($entries as $entry) {
            $data = $entry->id . '|' . 
                    $entry->account_id . '|' . 
                    $entry->currency_id . '|' . 
                    (float)$entry->debit . '|' . 
                    (float)$entry->credit . '|' . 
                    $entry->date . '|' . 
                    $previousHash;
            
            $hash = hash('sha256', $data);

            DB::table('journal_entries')
                ->where('id', $entry->id)
                ->update([
                    'previous_hash' => $previousHash,
                    'hash' => $hash
                ]);

            $previousHash = $hash;
        }
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['hash']);
            $table->dropColumn(['hash', 'previous_hash']);
        });
    }
};
