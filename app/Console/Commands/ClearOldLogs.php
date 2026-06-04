<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\ApiLog\Actions\ClearOldLogsAction;

class ClearOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear-old {days=30 : The threshold in days to clear logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes API request logs older than the specified threshold of days (defaults to 30 days)';

    /**
     * Execute the console command.
     */
    public function handle(ClearOldLogsAction $action)
    {
        $days = (int) $this->argument('days');
        $this->info("Clearing API request logs older than {$days} days...");

        $deletedCount = $action->execute($days);

        $this->info("Successfully deleted {$deletedCount} log(s) older than {$days} days.");
    }
}
