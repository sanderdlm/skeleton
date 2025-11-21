<?php

declare(strict_types=1);

namespace App\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ApiController implements ControllerInterface
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'status' => 'success',
            'data' => [
                'message' => 'Hello from the API',
                'timestamp' => time(),
            ],
        ]);
    }
}
