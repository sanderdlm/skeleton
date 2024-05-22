<?php

declare(strict_types=1);

use App\Controller\SearchController;
use App\Controller\HomeController;
use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Middlewares\ErrorFormatter\HtmlFormatter;
use Middlewares\ErrorHandler;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Dispatcher;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

use function FastRoute\simpleDispatcher;

require_once __DIR__ . '/../vendor/autoload.php';

// Initialize PSR-11 container
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);

// Initialize Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates');
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
    $r->addRoute('GET', '/search', SearchController::class);
});

// Build the middleware queue
$queue[] = new ErrorHandler([new HtmlFormatter()]);
$queue[] = new FastRoute($routes);
$queue[] = new RequestHandler($container);

// Handle the request
$dispatcher = new Dispatcher($queue);
$response = $dispatcher->dispatch(ServerRequestFactory::fromGlobals());

// 💨
(new SapiEmitter())->emit($response);
