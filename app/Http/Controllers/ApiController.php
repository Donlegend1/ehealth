<?php

namespace App\Http\Controllers;

use App\Domain\Weather\Actions\FetchWeatherAction;
use App\Domain\Weather\Requests\WeatherRequest;

class ApiController extends Controller
{
    /**
     * Fetch weather data (cached for 1 hour).
     */
    public function getWeather(WeatherRequest $request, FetchWeatherAction $action)
    {
        try {
            $dto = $request->toDto();
            $result = $action->execute($dto);

            return apiSuccess(
                data: $result['data'],
                extra: ['source' => $result['source']]
            );
        } catch (\Exception $e) {
            return apiError(
                message: $e->getMessage(),
                statusCode: 502
            );
        }
    }
}
