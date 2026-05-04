<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\AccountSummary;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class ReconcileBalances extends Command
{
    protected $signature = 'accounting:reconcile {--zero : Zero out Natron and Manual Customer}';
    protected $description = 'Recalculate account summaries from journal entries';

    public function handle()
    {
        $this->info('Starting balance reconciliation...');

        DB::transaction(function () {
            // 1. Clear current summaries
            AccountSummary::query()->delete();

            // 2. Re-aggregate from Journal Entries
            $entries = JournalEntry::select(
                'account_id',
                'currency_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->groupBy('account_id', 'currency_id')
            ->get();

            foreach ($entries as $entry) {
                AccountSummary::create([
                    'account_id' => $entry->account_id,
                    'currency_id' => $entry->currency_id,
                    'total_debit' => $entry->total_debit,
                    'total_credit' => $entry->total_credit
                ]);
            }

            // 3. Zero out specific accounts if requested
            if ($this->option('zero')) {
                $targetCodes = ['1201', '13']; // Manual Customer and Natron
                foreach ($targetCodes as $code) {
                    $account = Account::where('code', $code)->first();
                    if ($account) {
                        // We delete their journal entries and summaries to truly zero them
                        JournalEntry::where('account_id', $account->id)->delete();
                        AccountSummary::where('account_id', $account->id)->delete();
                        $this->warn("Account {$account->name} ({$code}) has been zeroed out.");
                    }
                }
            }
        });

        $this->info('Reconciliation completed successfully!');
    }
}
