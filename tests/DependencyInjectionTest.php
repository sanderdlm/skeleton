<?php

declare(strict_types=1);

namespace App\Test;

use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

final class DependencyInjectionTest extends AbstractTestCase
{
    public function testCanRetrieveTwigFromContainer(): void
    {
        $twig = $this->kernel->getService(Environment::class);

        static::assertInstanceOf(Environment::class, $twig);
    }

    public function testCanRetrieveFilesystemFromContainer(): void
    {
        $filesystem = $this->kernel->getService(Filesystem::class);

        static::assertInstanceOf(Filesystem::class, $filesystem);
    }

    public function testTwigServiceIsConfiguredCorrectly(): void
    {
        $twig = $this->kernel->getService(Environment::class);

        $loader = $twig->getLoader();
        static::assertTrue($loader->exists('base.twig'));
        static::assertTrue($loader->exists('pages/home.twig'));
    }

    public function testContainerAutoWiringWorks(): void
    {
        // If HomeController can be instantiated with its dependencies, autowiring works
        $response = $this->get('/');

        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Ready to start building', (string) $response->getBody());
    }

    public function testGetServiceThrowsExceptionForInvalidService(): void
    {
        $this->expectException(\Exception::class);

        $this->kernel->getService('NonExistentService');
    }
}
