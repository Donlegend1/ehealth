<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\ApiLog\DTOs\CreateApiRequestLogDTO;
use App\Domain\ApiLog\Actions\CreateApiRequestLogAction;

class LogApiRequests
{
    public function __construct(
        protected CreateApiRequestLogAction $createLogAction
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $durationMs = round(($endTime - $startTime) * 1000);

        try {
            $dto = CreateApiRequestLogDTO::fromParams(
                endpoint: $request->fullUrl(),
                method: $request->method(),
                statusCode: $response->getStatusCode(),
                ipAddress: $request->ip(),
                payload: json_encode($request->all(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                response: $response->getContent(),
                responseTimeMs: $durationMs
            );

            $this->createLogAction->execute($dto);
        } catch (\Exception $e) {
            logger()->error('Failed to log API request in DDD middleware: ' . $e->getMessage());
        }

        return $response;
    }
}
