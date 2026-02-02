<?php

declare(strict_types=1);

namespace App\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

final readonly class SessionController implements ControllerInterface
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $counter = $session->get('counter', 0);
        $counter++;
        $session->set('counter', $counter);

        return new JsonResponse([
            'counter' => $counter,
            'message' => 'Session counter incremented',
        ]);
    }
}
