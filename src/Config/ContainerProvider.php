<?php

declare(strict_types=1);

namespace App\Config;

use App\Service\DatabaseConnectionProvider;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

final readonly class ContainerProvider
{
    public function __construct(
        private string $projectRoot,
        private string $environment,
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

        $twig = $this->createTwig();
        $translator = $this->createTranslator();
        $twig->addExtension(new TranslationExtension($translator));

        $environment = $this->environment;
        $projectRoot = $this->projectRoot;
        $connectionProvider = new DatabaseConnectionProvider($environment, $projectRoot);

        $containerBuilder->addDefinitions([
            Environment::class => $twig,
            Translator::class => $translator,
            Connection::class => $connectionProvider(),
            DatabaseConnectionProvider::class => $connectionProvider,
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
        $twig->addExtension(new IntlExtension());

        return $twig;
    }

    private function createTranslator(): Translator
    {
        $translator = new Translator('en');
        $translator->addLoader('php', new PhpFileLoader());

        $files = glob($this->projectRoot . '/translations/messages.*.php');
        foreach ($files ?: [] as $file) {
            preg_match('/messages\.([a-z]{2})\.php$/', $file, $matches);
            if (isset($matches[1])) {
                $translator->addResource('php', $file, $matches[1]);
            }
        }

        $translator->setFallbackLocales(['en']);

        return $translator;
    }
}
