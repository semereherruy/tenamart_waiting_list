<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyReportMail;
use App\Models\WaitingList;

class SendWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:weekly-waiting-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a weekly waiting list report to admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 1. Gather the stats
        $total = WaitingList::count();
        $bySource = WaitingList::query()
            ->selectRaw('signup_source, COUNT(*) as count')
            ->groupBy('signup_source')
            ->pluck('count', 'signup_source')
            ->toArray();

        $start = now()->subDays(6)->startOfDay();
        $daily = WaitingList::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $stats = [
            'total_signups'      => $total,
            'signups_by_source'  => $bySource,
            'daily_trend_last_7' => $daily,
        ];

        // 2. Send the email
        Mail::to('admin@tenamart.local')
            ->send(new WeeklyReportMail($stats));

        $this->info('Weekly waiting list report sent successfully.');

        return 0;
    }
}
