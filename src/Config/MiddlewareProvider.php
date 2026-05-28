<?php

declare(strict_types=1);

namespace App\Config;

use App\Middleware\BodyParserMiddleware;
use App\Middleware\CsrfProtectionMiddleware;
use App\Middleware\SecurityHeadersMiddleware;
use FastRoute\Dispatcher;
use Lcobucci\JWT\Configuration as JwtConfiguration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Middlewares\ErrorFormatter\HtmlFormatter;
use Middlewares\ErrorHandler;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Dispatcher as MiddlewareDispatcher;
use Psr\Container\ContainerInterface;
use PSR7Sessions\Storageless\Http\Configuration as SessionConfiguration;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

final readonly class MiddlewareProvider
{
    public function create(ContainerInterface $container, Dispatcher $routes): MiddlewareDispatcher
    {
        $queue = [];
        $queue[] = new SecurityHeadersMiddleware();
        $queue[] = $this->createSessionMiddleware();
        $queue[] = new BodyParserMiddleware();
        $queue[] = new CsrfProtectionMiddleware();
        $queue[] = new ErrorHandler([new HtmlFormatter()]);
        $queue[] = new FastRoute($routes);
        $queue[] = new RequestHandler($container);

        return new MiddlewareDispatcher($queue);
    }

    private function createSessionMiddleware(): SessionMiddleware
    {
        $jwtSecret = $_ENV['JWT_SECRET'] ?? throw new \RuntimeException('JWT_SECRET environment variable is not set.');

        if (!is_string($jwtSecret) || $jwtSecret === '') {
            throw new \RuntimeException('JWT_SECRET must be a non-empty string.');
        }

        return new SessionMiddleware(
            SessionConfiguration::fromJwtConfiguration(
                JwtConfiguration::forSymmetricSigner(
                    new Sha256(),
                    InMemory::base64Encoded($jwtSecret),
                )
            )
        );
    }
}
