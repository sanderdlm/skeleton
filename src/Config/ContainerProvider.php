<?php

declare(strict_types=1);

namespace App\Config;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

final readonly class ContainerProvider
{
    public function __construct(
        private string $projectRoot,
        private bool $isDebug,
    ) {
    }

    public function create(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        if (!$this->isDebug) {
            $containerBuilder->enableCompilation($this->projectRoot . '/var/cache');
        }

        $containerBuilder->addDefinitions([
            Environment::class => $this->createTwig(),
        ]);

        return $containerBuilder->build();
    }

    private function createTwig(): Environment
    {
        $loader = new FilesystemLoader($this->projectRoot . '/templates');
        $twig = new Environment($loader, [
            'cache' => $this->isDebug ? false : $this->projectRoot . '/var/cache/twig',
            'debug' => $this->isDebug,
            'auto_reload' => $this->isDebug,
        ]);
        $twig->addExtension(new DebugExtension());

        return $twig;
    }
}
