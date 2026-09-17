<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_laravel_up_endpoint_returns_ok(): void
    {
        $response = $this->get('/up');
        $response->assertStatus(200);
    }

    public function test_api_health_endpoint_returns_healthy_services(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'environment',
                'services' => [
                    'database' => ['status', 'driver'],
                    'cache' => ['status', 'store'],
                    'storage' => ['status', 'disk'],
                ],
            ])
            ->assertJson([
                'status' => 'healthy',
                'services' => [
                    'database' => ['status' => 'ok'],
                    'cache' => ['status' => 'ok'],
                    'storage' => ['status' => 'ok'],
                ],
            ]);
    }

    public function test_health_check_command_succeeds(): void
    {
        $this->artisan('health:check')
            ->expectsOutputToContain('Checking Bunova local dependencies and service health...')
            ->expectsOutputToContain('[PASS] Database')
            ->expectsOutputToContain('[PASS] Cache')
            ->expectsOutputToContain('[PASS] Storage')
            ->expectsOutputToContain('All services healthy.')
            ->assertExitCode(0);
    }
}
