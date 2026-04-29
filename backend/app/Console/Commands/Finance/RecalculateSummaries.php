<?php

namespace App\Console\Commands\Finance;

use Illuminate\Console\Command;
use App\Models\AccountSummary;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class RecalculateSummaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:recalculate-summaries {account_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculates all account summaries from journal entries to ensure data integrity.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $accountId = $this->argument('account_id');
        
        $this->info('Starting recalculation of account summaries...');

        DB::beginTransaction();
        try {
            if ($accountId) {
                AccountSummary::where('account_id', $accountId)->delete();
                $this->info("Cleared summaries for account #{$accountId}");
            } else {
                AccountSummary::query()->delete();
                $this->info('Cleared all account summaries.');
            }

            // Aggregate journal entries
            $query = JournalEntry::select(
                'account_id',
                'currency_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit'),
                DB::raw('MAX(updated_at) as last_updated')
            )->groupBy('account_id', 'currency_id');

            if ($accountId) {
                $query->where('account_id', $accountId);
            }

            $summaries = $query->get();

            foreach ($summaries as $s) {
                AccountSummary::create([
                    'account_id' => $s->account_id,
                    'currency_id' => $s->currency_id,
                    'total_debit' => $s->total_debit,
                    'total_credit' => $s->total_credit,
                    'created_at' => $s->last_updated,
                    'updated_at' => $s->last_updated,
                ]);
            }

            DB::commit();
            $this->info('Successfully recalculated ' . $summaries->count() . ' summaries.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to recalculate: ' . $e->getMessage());
        }
    }
}
