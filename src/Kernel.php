<?php

declare(strict_types=1);

namespace App;

use App\Config\ContainerProvider;
use App\Config\MiddlewareProvider;
use App\Config\RouteProvider;
use Dotenv\Dotenv;
use Middlewares\Utils\Dispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class Kernel
{
    private ContainerInterface $container;
    private Dispatcher $dispatcher;

    public function __construct(string $projectRoot)
    {
        $dotenv = Dotenv::createImmutable($projectRoot);
        $dotenv->safeLoad();

        $isDebug = ($_ENV['APP_ENV'] ?? 'dev') !== 'prod';

        $this->container = new ContainerProvider($projectRoot, $isDebug)->create();
        $this->dispatcher = new MiddlewareProvider()->create(
            container: $this->container,
            routes: new RouteProvider($isDebug)->create(),
        );
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
