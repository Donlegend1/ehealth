<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Domain\ApiLog\Models\ApiRequestLog;
use Carbon\Carbon;
use Tests\TestCase;

class ApiSchedulerCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear cache before each test
        Cache::clear();
    }

    /**
     * Test weather API endpoint returns correct mock data and caches it.
     */
    public function test_weather_endpoint_fetches_and_caches_data()
    {
        // Fake the external Open-Meteo API response
        Http::fake([
            'api.open-meteo.com/*' => Http::response([
                'current' => [
                    'temperature_2m' => 22.5,
                    'wind_speed_10m' => 12.3
                ]
            ], 200)
        ]);

        // First Request: Should fetch from the "external_api" source
        $response1 = $this->getJson('/api/weather?latitude=52.52&longitude=13.41');
        $response1->assertStatus(200)
                  ->assertJsonPath('success', true)
                  ->assertJsonPath('source', 'external_api')
                  ->assertJsonStructure(['data' => ['current']]);

        // Second Request: Should retrieve from the "cache" source without hitting the fake HTTP again
        $response2 = $this->getJson('/api/weather?latitude=52.52&longitude=13.41');
        $response2->assertStatus(200)
                  ->assertJsonPath('success', true)
                  ->assertJsonPath('source', 'cache');

        // Assert only 1 HTTP call was recorded
        Http::assertSentCount(1);
    }

    /**
     * Test that incoming request and response are logged in the database.
     */
    public function test_incoming_requests_are_logged_to_database()
    {
        Http::fake([
            'api.open-meteo.com/*' => Http::response(['data' => 'test'], 200)
        ]);

        $this->getJson('/api/weather?latitude=10.0&longitude=20.0');

        $this->assertDatabaseHas('api_request_logs', [
            'method' => 'GET',
            'status_code' => 200,
        ]);

        $log = ApiRequestLog::first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('/api/weather', $log->endpoint);
        $this->assertNotNull($log->response_time_ms);
    }

    /**
     * Test logs:clear-old command deletes log entries older than 30 days.
     */
    public function test_clear_old_logs_command_deletes_records_older_than_30_days()
    {
        // Create a log created now (should be kept)
        $newLog = ApiRequestLog::create([
            'endpoint' => 'http://localhost/api/weather',
            'method' => 'GET',
            'status_code' => 200,
        ]);

        // Create a log created 31 days ago (should be deleted)
        Carbon::setTestNow(now()->subDays(31));
        $oldLog = ApiRequestLog::create([
            'endpoint' => 'http://localhost/api/weather',
            'method' => 'GET',
            'status_code' => 200,
        ]);
        
        // Reset time back to present
        Carbon::setTestNow();

        // Verify both logs exist in DB
        $this->assertDatabaseCount('api_request_logs', 2);

        // Run the clear-old logs Artisan command
        $this->artisan('logs:clear-old')
             ->expectsOutput('Clearing API request logs older than 30 days...')
             ->expectsOutput('Successfully deleted 1 log(s) older than 30 days.')
             ->assertExitCode(0);

        // Assert only the new log remains
        $this->assertDatabaseCount('api_request_logs', 1);
        $this->assertDatabaseHas('api_request_logs', ['id' => $newLog->id]);
        $this->assertDatabaseMissing('api_request_logs', ['id' => $oldLog->id]);
    }

    /**
     * Test api:fetch-weather command retrieves and caches data.
     */
    public function test_api_fetch_weather_command_updates_cache()
    {
        Http::fake([
            'api.open-meteo.com/*' => Http::response(['mock_key' => 'mock_val'], 200)
        ]);

        $this->artisan('api:fetch-weather --latitude=40.71 --longitude=-74.00')
             ->expectsOutput('Source: external_api')
             ->assertExitCode(0);

        // Verify the cache has the correct key and structure
        $this->assertTrue(Cache::has('weather_data_40.71_-74'));
        $cachedData = Cache::get('weather_data_40.71_-74');
        $this->assertEquals('mock_val', $cachedData['mock_key']);
    }
}
