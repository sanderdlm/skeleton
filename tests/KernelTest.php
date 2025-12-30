<?php

declare(strict_types=1);

namespace App\Test;

use App\Kernel;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;

final class KernelTest extends TestCase
{
    public function testKernelInitializationInDevMode(): void
    {
        // Force dev environment to disable caching and ensure callback execution
        $_ENV['APP_ENV'] = 'dev';

        try {
            $kernel = new Kernel(__DIR__ . '/..');

            // Test that routes are properly registered by dispatching requests
            $homeRequest = new ServerRequest(
                serverParams: [],
                uploadedFiles: [],
                uri: '/',
                method: 'GET',
                body: 'php://memory',
            );

            $homeResponse = $kernel->dispatch($homeRequest);
            static::assertSame(200, $homeResponse->getStatusCode());

            $apiRequest = new ServerRequest(
                serverParams: [],
                uploadedFiles: [],
                uri: '/api/status',
                method: 'GET',
                body: 'php://memory',
            );

            $apiResponse = $kernel->dispatch($apiRequest);
            static::assertSame(200, $apiResponse->getStatusCode());
        } finally {
            // Clean up environment variable
            unset($_ENV['APP_ENV']);
        }
    }
}
