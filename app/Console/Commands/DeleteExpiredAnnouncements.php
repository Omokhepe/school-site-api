<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;

class DeleteExpiredAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-announcements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Announcements whose end_date is after today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Announcement::where('end_date','<', now()->subDays(0))->delete();

        $count = Announcement::where('end_date', '<', now()->toDateString())->delete();
        $this->info("Deleted {$count} expired announcements.");
        return 0;
    }
}