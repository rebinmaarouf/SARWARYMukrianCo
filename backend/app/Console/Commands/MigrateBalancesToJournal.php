<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\JournalService;
use Illuminate\Support\Facades\DB;

class MigrateBalancesToJournal extends Command
{
    protected $signature = 'accounting:migrate';
    protected $description = 'Migrate legacy balances to the new Journal Entry system';

    public function handle()
    {
        $this->info('Starting data migration to Unified Accounting System...');

        DB::transaction(function () {
            // 1. Ensure Base Exchange Rates exist
            $this->seedExchangeRates();

            // 2. Migrate Legacy Account Balances
            $accounts = Account::all();
            $count = 0;
            foreach ($accounts as $account) {
                if ($account->balance != 0) {
                    $this->createOpeningBalance($account, 1, $account->balance); // Assuming IQD (ID: 1)
                    $count++;
                }
            }
            
            $this->info("Migrated {$count} account balances.");
        });

        $this->info('Migration completed successfully!');
        return 0;
    }

    private function createOpeningBalance($account, $currencyId, $balance)
    {
        // JournalService requires a model as 'entryable'. 
        // For opening balances, we can link them to the account itself or a dummy system entry.
        JournalService::record(
            $account, 
            $account->id, 
            $currencyId, 
            $balance > 0 ? $balance : 0, 
            $balance < 0 ? abs($balance) : 0, 
            'Opening Balance (گواستنەوەی باڵانس)', 
            now()->startOfYear()->format('Y-m-d'),
            1.0 // Opening balances are usually at base rate 1.0 or historical
        );
    }

    private function seedExchangeRates()
    {
        $usd = Currency::where('code', 'USD')->first();
        if ($usd) {
            ExchangeRate::updateOrCreate(
                ['currency_id' => $usd->id, 'date' => now()->format('Y-m-d')],
                ['rate' => 1500]
            );
        }
    }
}
