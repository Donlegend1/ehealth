<?php

namespace App\Domain\ApiLog\Actions;

use App\Domain\ApiLog\DTOs\CreateApiRequestLogDTO;
use App\Domain\ApiLog\Models\ApiRequestLog;

class CreateApiRequestLogAction
{
    /**
     * Execute writing of log to database.
     */
    public function execute(CreateApiRequestLogDTO $dto): ApiRequestLog
    {
        return ApiRequestLog::create([
            'endpoint' => $dto->endpoint,
            'method' => $dto->method,
            'status_code' => $dto->statusCode,
            'ip_address' => $dto->ipAddress,
            'payload' => $dto->payload,
            'response' => $dto->response,
            'response_time_ms' => $dto->responseTimeMs,
        ]);
    }
}
