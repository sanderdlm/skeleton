<?php

declare(strict_types=1);

namespace App\Config;

use App\Controller\HomeController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\cachedDispatcher;
use function FastRoute\simpleDispatcher;

final readonly class RouteProvider
{
    public function __construct(
        private bool $isDebug,
    ) {
    }

    public function create(): Dispatcher
    {
        $callback = static function (RouteCollector $r): void {
            $r->addRoute('GET', '/', HomeController::class);
        };

        if ($this->isDebug) {
            return simpleDispatcher($callback);
        }

        return cachedDispatcher($callback, [
            'cacheFile' => __DIR__ . '/../../var/cache/routes.cache.php',
        ]);
    }
}
