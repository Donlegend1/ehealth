<?php

namespace App\Domain\ApiLog\DTOs;

class CreateApiRequestLogDTO
{
    public function __construct(
        public readonly string $endpoint,
        public readonly string $method,
        public readonly ?int $statusCode,
        public readonly ?string $ipAddress,
        public readonly ?string $payload,
        public readonly ?string $response,
        public readonly ?int $responseTimeMs
    ) {}

    public static function fromParams(
        string $endpoint,
        string $method,
        ?int $statusCode,
        ?string $ipAddress,
        ?string $payload,
        ?string $response,
        ?int $responseTimeMs
    ): self {
        return new self(
            endpoint: $endpoint,
            method: $method,
            statusCode: $statusCode,
            ipAddress: $ipAddress,
            payload: $payload,
            response: $response,
            responseTimeMs: $responseTimeMs
        );
    }
}
