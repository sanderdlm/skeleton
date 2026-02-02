<?php

declare(strict_types=1);

namespace App\Test;

use Symfony\Component\Filesystem\Filesystem;

final class CacheTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        // Force dev environment to prod for cache testing purposes
        $_ENV['APP_ENV'] = 'prod';

        parent::setUp();
    }

    public function testAppEnvIsSetToProd(): void
    {
        static::assertSame('prod', $_ENV['APP_ENV']);
    }

    public function testCacheFolderIsInitialized(): void
    {
        // Make a request to trigger cache creation
        $response = $this->get('/');

        static::assertSame(200, $response->getStatusCode());

        $cacheFolder = __DIR__ . '/../var/cache';
        $twigCacheFolder = $cacheFolder . '/twig';
        $containerCache = $cacheFolder . '/CompiledContainer.php';
        $routerCache = $cacheFolder . '/routes.cache.php';

        static::assertDirectoryExists($cacheFolder);
        static::assertDirectoryExists($twigCacheFolder);
        static::assertFileExists($containerCache);
        static::assertFileExists($routerCache);
    }

    protected function tearDown(): void
    {
        $filesystem = $this->kernel->getService(FileSystem::class);

        $filesystem->remove(__DIR__ . '/../var/cache');

        $_ENV['APP_ENV'] = 'test';
    }
}
