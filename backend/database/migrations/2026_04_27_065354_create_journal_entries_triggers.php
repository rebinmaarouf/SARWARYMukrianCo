<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. After Insert
        DB::unprepared("
            CREATE TRIGGER tr_journal_entries_after_insert
            AFTER INSERT ON journal_entries
            FOR EACH ROW
            BEGIN
                INSERT INTO account_summaries (account_id, currency_id, total_debit, total_credit, created_at, updated_at)
                VALUES (NEW.account_id, NEW.currency_id, NEW.debit, NEW.credit, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    total_debit = total_debit + NEW.debit,
                    total_credit = total_credit + NEW.credit,
                    updated_at = NOW();
            END
        ");

        // 2. After Update
        DB::unprepared("
            CREATE TRIGGER tr_journal_entries_after_update
            AFTER UPDATE ON journal_entries
            FOR EACH ROW
            BEGIN
                -- Subtract old values
                UPDATE account_summaries
                SET total_debit = total_debit - OLD.debit,
                    total_credit = total_credit - OLD.credit,
                    updated_at = NOW()
                WHERE account_id = OLD.account_id AND currency_id = OLD.currency_id;

                -- Add new values
                INSERT INTO account_summaries (account_id, currency_id, total_debit, total_credit, created_at, updated_at)
                VALUES (NEW.account_id, NEW.currency_id, NEW.debit, NEW.credit, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    total_debit = total_debit + NEW.debit,
                    total_credit = total_credit + NEW.credit,
                    updated_at = NOW();
            END
        ");

        // 3. After Delete
        DB::unprepared("
            CREATE TRIGGER tr_journal_entries_after_delete
            AFTER DELETE ON journal_entries
            FOR EACH ROW
            BEGIN
                UPDATE account_summaries
                SET total_debit = total_debit - OLD.debit,
                    total_credit = total_credit - OLD.credit,
                    updated_at = NOW()
                WHERE account_id = OLD.account_id AND currency_id = OLD.currency_id;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS tr_journal_entries_after_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_journal_entries_after_update");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_journal_entries_after_delete");
    }
};
