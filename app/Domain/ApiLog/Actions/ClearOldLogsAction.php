<?php

namespace App\Domain\ApiLog\Actions;

use App\Domain\ApiLog\Models\ApiRequestLog;

class ClearOldLogsAction
{
    /**
     * Execute deletion of logs older than threshold days.
     */
    public function execute(int $days = 30): int
    {
        $cutoffDate = now()->subDays($days);
        return ApiRequestLog::where('created_at', '<', $cutoffDate)->delete();
    }
}
