<?php

declare(strict_types=1);

namespace App\Test;

use App\Kernel;
use Symfony\Component\Filesystem\Filesystem;

final class CacheTest extends AbstractTestCase
{
    private Kernel $prodKernel;

    protected function setUp(): void
    {
        $_ENV['APP_ENV'] = 'prod';
        $this->prodKernel = new Kernel(__DIR__ . '/..');
        parent::setUp();
    }

    public function testAppEnvIsSetToProd(): void
    {
        static::assertSame('prod', $_ENV['APP_ENV']);
    }

    public function testCacheFolderIsInitialized(): void
    {
        $request = new \Laminas\Diactoros\ServerRequest(
            serverParams: [],
            uploadedFiles: [],
            uri: '/',
            method: 'GET',
            body: 'php://memory',
        );
        $response = $this->prodKernel->dispatch($request);
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
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__ . '/../var/cache');
        $_ENV['APP_ENV'] = 'test';

        parent::tearDown();
    }
}
