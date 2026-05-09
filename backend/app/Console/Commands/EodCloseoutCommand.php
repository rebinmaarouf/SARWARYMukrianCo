<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\CloseoutBalancesMail;
use Carbon\Carbon;

class EodCloseoutCommand extends Command
{
    protected $signature = 'finance:eod-closeout';
    protected $description = 'Gather and email daily closing balances to the owner and all manager-level users.';

    public function handle()
    {
        $this->info('Starting EOD Closeout...');

        $vaults = DB::table('account_summaries')
            ->join('accounts', 'account_summaries.account_id', '=', 'accounts.id')
            ->join('currencies', 'account_summaries.currency_id', '=', 'currencies.id')
            ->where('accounts.type', 'vault')
            ->select('accounts.name', 'currencies.code as currency_code', DB::raw('(total_debit - total_credit) as balance'))
            ->get();

        $date = Carbon::today()->toDateString();

        $emails = ['rebin.maaruf@gmail.com'];

        try {
            $managers = User::role('manager')->get();
            foreach ($managers as $m) {
                if ($m->email && filter_var($m->email, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $m->email;
                }
            }
        } catch (\Throwable $e) {
            $this->warn('Spatie role "manager" resolution skipped: ' . $e->getMessage());
        }

        $emails = array_unique($emails);

        foreach ($emails as $email) {
            $ownerName = ($email === 'rebin.maaruf@gmail.com') ? 'کاک ڕێبین' : 'بەڕێز مانیجەر';
            Mail::to($email)->send(new CloseoutBalancesMail($date, $vaults, $ownerName));
            $this->info("EOD closeout report emailed to: {$email}");
        }

        $this->info('EOD Closeout completed successfully!');
    }
}
