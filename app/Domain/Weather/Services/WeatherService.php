<?php

namespace App\Domain\Weather\Services;

use Illuminate\Support\Facades\Http;
use App\Domain\Weather\DTOs\WeatherQueryDTO;

class WeatherService
{
    /**
     * Fetch weather data directly from Open-Meteo external service.
     */
    public function fetchFromExternalApi(WeatherQueryDTO $dto): array
    {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$dto->latitude}&longitude={$dto->longitude}&current=temperature_2m,wind_speed_10m";

        $response = Http::timeout(10)->get($url);

        if ($response->failed()) {
            throw new \RuntimeException("Failed to fetch weather data from external API. HTTP Status: " . $response->status());
        }

        return $response->json();
    }
}
