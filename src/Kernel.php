<?php

declare(strict_types=1);

namespace App;

use App\Controller\ApiController;
use App\Controller\HomeController;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use FastRoute\RouteCollector;
use Middlewares\ErrorFormatter\HtmlFormatter;
use Middlewares\ErrorHandler;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

use function FastRoute\cachedDispatcher;

final readonly class Kernel
{
    private Dispatcher $dispatcher;

    public function __construct(string $projectRoot)
    {
        // Load environment variables
        $dotenv = Dotenv::createImmutable($projectRoot);
        $dotenv->safeLoad();

        $isDebug = array_key_exists('APP_ENV', $_ENV) && $_ENV['APP_ENV'] === 'dev';

        // Initialize PSR-11 container
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        if (!$isDebug) {
            $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
        }

        // Initialize Twig
        $loader = new FilesystemLoader($projectRoot . '/templates');
        $twig = new Environment($loader, [
            'cache' => __DIR__ . '/../var/cache/twig',
            'debug' => $isDebug,
            'auto_reload' => $isDebug,
        ]);

        $twig->addExtension(new DebugExtension());

        // Set container definitions
        $containerBuilder->addDefinitions([
            Environment::class => $twig,
        ]);

        $container = $containerBuilder->build();

        // Define the routes
        $routes = cachedDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/', HomeController::class);
            $r->addRoute('GET', '/api/status', ApiController::class);
        }, [
            'cacheFile' => __DIR__ . '/../var/cache/routes.cache.php',
            'cacheDisabled' => $isDebug,
        ]);

        // Build the middleware queue
        $queue[] = new ErrorHandler([new HtmlFormatter()]);
        $queue[] = new FastRoute($routes);
        $queue[] = new RequestHandler($container);

        $this->dispatcher = new Dispatcher($queue);
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        return $this->dispatcher->dispatch($request);
    }
}
