<?php

declare(strict_types=1);

namespace App;

use App\Controller\HomeController;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use FastRoute\RouteCollector;
use Lcobucci\JWT\Configuration as JwtConfiguration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Middlewares\ErrorFormatter\HtmlFormatter;
use Middlewares\ErrorHandler;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Dispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Sessions\Storageless\Http\Configuration as SessionConfiguration;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

use function FastRoute\cachedDispatcher;

final readonly class Kernel
{
    private ContainerInterface $container;
    private Dispatcher $dispatcher;

    public function __construct(string $projectRoot)
    {
        // Load environment variables
        $dotenv = Dotenv::createImmutable($projectRoot);
        $dotenv->safeLoad();

        // Determine environment
        $isDebug = array_key_exists('APP_ENV', $_ENV) && $_ENV['APP_ENV'] !== 'prod';

        // Initialize PSR-11 container
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        if (!$isDebug) {
            $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
        }

        // Initialize Twig
        $loader = new FilesystemLoader($projectRoot . '/templates');
        $twig = new Environment($loader, [
            'cache' => $isDebug ? false : __DIR__ . '/../var/cache/twig',
            'debug' => $isDebug,
            'auto_reload' => $isDebug,
        ]);
        $twig->addExtension(new DebugExtension());

        // Set container definitions
        $containerBuilder->addDefinitions([
            Environment::class => $twig,
        ]);

        $this->container = $containerBuilder->build();

        // Define the routes
        $routes = cachedDispatcher(static function (RouteCollector $r) {
            $r->addRoute('GET', '/', HomeController::class);
        }, [
            'cacheFile' => __DIR__ . '/../var/cache/routes.cache.php',
            'cacheDisabled' => $isDebug,
        ]);

        // Initialize the JWT session middleware
        $sessionMiddleware = new SessionMiddleware(
            SessionConfiguration::fromJwtConfiguration(
                JwtConfiguration::forSymmetricSigner(
                    new Sha256(),
                    InMemory::base64Encoded('OpcMuKmoxkhzW0Y1iESpjWwL/D3UBdDauJOe742BJ5Q='),
                )
            )
        );

        // Build the middleware queue
        $queue = [];
        $queue[] = $sessionMiddleware;
        $queue[] = new ErrorHandler([new HtmlFormatter()]);
        $queue[] = new FastRoute($routes);
        $queue[] = new RequestHandler($this->container);

        $this->dispatcher = new Dispatcher($queue);
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        return $this->dispatcher->dispatch($request);
    }

    /**
     * @template T of object
     * @param class-string<T> $className
     * @return T
     */
    public function getService(string $className): object
    {
        $service = $this->container->get($className);

        if (!$service instanceof $className) {
            throw new \RuntimeException(sprintf(
                'Requested service %s did not return a valid instance.',
                $className,
            ));
        }

        /** @var T $service */
        return $service;
    }
}
