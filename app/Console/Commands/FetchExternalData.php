<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Weather\DTOs\WeatherQueryDTO;
use App\Domain\Weather\Actions\FetchWeatherAction;

class FetchExternalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:fetch-weather {--latitude=52.52} {--longitude=13.41} {--force : Force refresh cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches weather data from external API and caches the response for 1 hour';

    /**
     * Execute the console command.
     */
    public function handle(FetchWeatherAction $action)
    {
        $lat = (float) $this->option('latitude');
        $lon = (float) $this->option('longitude');
        $force = (bool) $this->option('force');

        $dto = new WeatherQueryDTO($lat, $lon);

        if ($force) {
            $this->info("Force option detected. Clearing old cache...");
        }

        try {
            $result = $action->execute($dto, $force);

            $this->info("Source: " . $result['source']);
            $this->line(json_encode($result['data'], JSON_PRETTY_PRINT));
            return 0;
        } catch (\Exception $e) {
            $this->error("Error occurred: " . $e->getMessage());
            return 1;
        }
    }
}
