<?php

namespace Tests\Feature;

use App\Support\Config\EnvironmentValidator;
use Tests\TestCase;

class EnvironmentConfigTest extends TestCase
{
    protected EnvironmentValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new EnvironmentValidator();
    }

    public function test_testing_environment_passes_validation(): void
    {
        $result = $this->validator->validate([
            'app.env' => 'testing',
            'app.key' => 'base64:44m8u+x4vL+f4nZ8s1d4y7w2x5q8r9t0y1u2i3o4p5a=',
            'app.debug' => true,
            'database.default' => 'sqlite',
        ]);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    public function test_missing_app_key_fails_validation(): void
    {
        $result = $this->validator->validate([
            'app.env' => 'local',
            'app.key' => '',
        ]);

        $this->assertFalse($result['valid']);
        $this->assertContains('APP_KEY is empty. Run `php artisan key:generate` to set it.', $result['errors']);
    }

    public function test_production_environment_rejects_debug_mode(): void
    {
        $result = $this->validator->validate([
            'app.env' => 'production',
            'app.key' => 'base64:44m8u+x4vL+f4nZ8s1d4y7w2x5q8r9t0y1u2i3o4p5a=',
            'app.debug' => true,
            'database.default' => 'pgsql',
            'database.connections.pgsql.password' => 'super-secret-secure-password',
            'bunova.device_secret' => 'valid-32-character-device-secret-12345',
            'bunova.platform_secret' => 'valid-32-character-platform-secret-12345',
        ]);

        $this->assertFalse($result['valid']);
        $this->assertContains('APP_DEBUG must be false in production.', $result['errors']);
    }

    public function test_production_environment_rejects_in_memory_sqlite(): void
    {
        $result = $this->validator->validate([
            'app.env' => 'production',
            'app.key' => 'base64:44m8u+x4vL+f4nZ8s1d4y7w2x5q8r9t0y1u2i3o4p5a=',
            'app.debug' => false,
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'bunova.device_secret' => 'valid-32-character-device-secret-12345',
            'bunova.platform_secret' => 'valid-32-character-platform-secret-12345',
        ]);

        $this->assertFalse($result['valid']);
        $this->assertContains('In-memory SQLite cannot be used as primary database in production.', $result['errors']);
    }

    public function test_production_environment_requires_secrets_and_rejects_placeholders(): void
    {
        $result = $this->validator->validate([
            'app.env' => 'production',
            'app.key' => 'base64:44m8u+x4vL+f4nZ8s1d4y7w2x5q8r9t0y1u2i3o4p5a=',
            'app.debug' => false,
            'database.default' => 'pgsql',
            'database.connections.pgsql.password' => 'secret-db-password',
            'bunova.device_secret' => 'your-secret-placeholder-token-here',
            'bunova.platform_secret' => 'change-me-later-admin-platform-secret',
        ]);

        $this->assertFalse($result['valid']);
        $this->assertContains('BUNOVA_DEVICE_TOKEN_SECRET cannot be a generic placeholder.', $result['errors']);
        $this->assertContains('BUNOVA_PLATFORM_OPERATOR_SECRET cannot be a generic placeholder.', $result['errors']);
    }

    public function test_production_environment_passes_when_fully_configured(): void
    {
        $result = $this->validator->validate([
            'app.env' => 'production',
            'app.key' => 'base64:44m8u+x4vL+f4nZ8s1d4y7w2x5q8r9t0y1u2i3o4p5a=',
            'app.debug' => false,
            'database.default' => 'pgsql',
            'database.connections.pgsql.password' => 'strong-random-db-password-999!',
            'session.secure' => true,
            'bunova.device_secret' => 'secure-production-device-token-secret-998877',
            'bunova.platform_secret' => 'secure-production-platform-operator-secret-112233',
        ]);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    public function test_env_example_is_secret_safe(): void
    {
        $envExamplePath = base_path('.env.example');
        $this->assertFileExists($envExamplePath);

        $content = file_get_contents($envExamplePath);
        $this->assertNotEmpty($content);

        // Assert no live base64 keys or hardcoded passwords in .env.example
        $this->assertMatchesRegularExpression('/APP_KEY=\s*$/m', $content, '.env.example must have empty APP_KEY.');
        $this->assertMatchesRegularExpression('/DB_PASSWORD=\s*$/m', $content, '.env.example must have empty DB_PASSWORD.');
        $this->assertMatchesRegularExpression('/BUNOVA_DEVICE_TOKEN_SECRET=\s*$/m', $content, '.env.example must have empty BUNOVA_DEVICE_TOKEN_SECRET.');
        $this->assertMatchesRegularExpression('/BUNOVA_PLATFORM_OPERATOR_SECRET=\s*$/m', $content, '.env.example must have empty BUNOVA_PLATFORM_OPERATOR_SECRET.');
    }
}
