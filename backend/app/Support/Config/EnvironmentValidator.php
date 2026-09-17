<?php

namespace App\Support\Config;

use InvalidArgumentException;

class EnvironmentValidator
{
    /**
     * Forbidden placeholder values in non-local environments.
     */
    protected const FORBIDDEN_PLACEHOLDERS = [
        'your-secret',
        'your-app-key',
        'change-me',
        'changeme',
        'secret',
        'password',
        'root',
        'admin',
        'todo',
        'placeholder',
    ];

    /**
     * Validate the given configuration array or environment state.
     *
     * @param array<string, mixed>|null $config Optional config array (defaults to live config)
     * @return array{valid: bool, errors: list<string>, warnings: list<string>}
     */
    public function validate(?array $config = null): array
    {
        $env = $config['app.env'] ?? config('app.env', 'local');
        $debug = $config['app.debug'] ?? config('app.debug', false);
        $key = $config['app.key'] ?? config('app.key', '');
        $dbConnection = $config['database.default'] ?? config('database.default', 'sqlite');
        $sessionSecure = $config['session.secure'] ?? config('session.secure', false);
        $deviceSecret = $config['bunova.device_secret'] ?? env('BUNOVA_DEVICE_TOKEN_SECRET');
        $platformSecret = $config['bunova.platform_secret'] ?? env('BUNOVA_PLATFORM_OPERATOR_SECRET');

        $errors = [];
        $warnings = [];

        // Universal validation
        if (empty($key)) {
            $errors[] = 'APP_KEY is empty. Run `php artisan key:generate` to set it.';
        } elseif (!str_starts_with($key, 'base64:') && strlen($key) < 32) {
            $errors[] = 'APP_KEY is not a valid 32+ character or base64-encoded key.';
        }

        // Production & Staging strict validations
        if ($env === 'production') {
            if ($debug === true) {
                $errors[] = 'APP_DEBUG must be false in production.';
            }

            if ($dbConnection === 'sqlite') {
                $dbDatabase = $config['database.connections.sqlite.database'] ?? config('database.connections.sqlite.database');
                if ($dbDatabase === ':memory:') {
                    $errors[] = 'In-memory SQLite cannot be used as primary database in production.';
                } else {
                    $warnings[] = 'Production is configured with SQLite instead of recommended PostgreSQL.';
                }
            }

            if (in_array($dbConnection, ['pgsql', 'mysql'], true)) {
                $dbPassword = $config["database.connections.{$dbConnection}.password"] ?? config("database.connections.{$dbConnection}.password");
                if (empty($dbPassword)) {
                    $errors[] = "Production database password for [{$dbConnection}] cannot be empty.";
                }
            }

            if (!$sessionSecure) {
                $warnings[] = 'SESSION_SECURE_COOKIE should be true in production to enforce HTTPS session transmission.';
            }

            if (empty($deviceSecret)) {
                $errors[] = 'BUNOVA_DEVICE_TOKEN_SECRET is required in production for registered device token signing.';
            } elseif (strlen($deviceSecret) < 32) {
                $errors[] = 'BUNOVA_DEVICE_TOKEN_SECRET must be at least 32 characters in production.';
            } elseif ($this->isPlaceholder($deviceSecret)) {
                $errors[] = 'BUNOVA_DEVICE_TOKEN_SECRET cannot be a generic placeholder.';
            }

            if (empty($platformSecret)) {
                $errors[] = 'BUNOVA_PLATFORM_OPERATOR_SECRET is required in production for Platform Admin validation.';
            } elseif (strlen($platformSecret) < 32) {
                $errors[] = 'BUNOVA_PLATFORM_OPERATOR_SECRET must be at least 32 characters in production.';
            } elseif ($this->isPlaceholder($platformSecret)) {
                $errors[] = 'BUNOVA_PLATFORM_OPERATOR_SECRET cannot be a generic placeholder.';
            }
        } elseif ($env === 'staging') {
            if ($debug === true) {
                $warnings[] = 'APP_DEBUG is enabled in staging; ensure sensitive stack traces are not public.';
            }
            if (empty($deviceSecret)) {
                $warnings[] = 'BUNOVA_DEVICE_TOKEN_SECRET is recommended for device registration testing in staging.';
            }
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Check if a given secret matches known placeholder strings.
     */
    protected function isPlaceholder(string $value): bool
    {
        $normalized = strtolower(trim($value));
        if (in_array($normalized, self::FORBIDDEN_PLACEHOLDERS, true)) {
            return true;
        }
        foreach (['your-secret', 'your-app-key', 'change-me', 'changeme', 'placeholder'] as $phrase) {
            if (str_contains($normalized, $phrase)) {
                return true;
            }
        }
        return false;
    }
}
