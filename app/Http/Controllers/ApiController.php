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

            return response()->json([
                'success' => true,
                'source' => $result['source'],
                'data' => $result['data']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 502);
        }
    }
}
