<?php

namespace App\Domain\Weather\DTOs;

class WeatherQueryDTO
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            latitude: (float) ($data['latitude'] ?? 52.52),
            longitude: (float) ($data['longitude'] ?? 13.41)
        );
    }
}
