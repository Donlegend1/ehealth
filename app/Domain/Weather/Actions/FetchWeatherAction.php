<?php

namespace App\Domain\Weather\Actions;

use App\Domain\Weather\DTOs\WeatherQueryDTO;
use App\Domain\Weather\Services\WeatherService;
use Illuminate\Support\Facades\Cache;

class FetchWeatherAction
{
    public function __construct(
        protected WeatherService $weatherService
    ) {}

    /**
     * Execute fetching of weather data (resolves cache and external service).
     */
    public function execute(WeatherQueryDTO $dto, bool $forceRefresh = false): array
    {
        $cacheKey = "weather_data_{$dto->latitude}_{$dto->longitude}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        $isCached = Cache::has($cacheKey);

        $weatherData = Cache::remember($cacheKey, 3600, function () use ($dto) {
            return $this->weatherService->fetchFromExternalApi($dto);
        });

        return [
            'source' => $isCached ? 'cache' : 'external_api',
            'data' => $weatherData
        ];
    }
}
