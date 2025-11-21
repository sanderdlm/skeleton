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

use function FastRoute\simpleDispatcher;

final readonly class Kernel
{
    private Dispatcher $dispatcher;

    public function __construct(string $projectRoot)
    {
        // Load environment variables
        $dotenv = Dotenv::createImmutable($projectRoot);
        $dotenv->safeLoad();

        // Initialize PSR-11 container
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);

        // Initialize Twig
        $loader = new FilesystemLoader($projectRoot . '/templates');
        $twig = new Environment($loader, ['debug' => true]);
        $twig->addExtension(new DebugExtension());

        // Set container definitions
        $containerBuilder->addDefinitions([
            Environment::class => $twig,
        ]);

        $container = $containerBuilder->build();

        // Define the routes
        $routes = simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/', HomeController::class);
            $r->addRoute('GET', '/api/status', ApiController::class);
        });

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
