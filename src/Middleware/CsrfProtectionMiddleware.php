<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class CsrfProtectionMiddleware implements MiddlewareInterface
{
    private const array SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(), self::SAFE_METHODS, true)) {
            return $handler->handle($request);
        }

        $secFetchSite = $request->getHeader('Sec-Fetch-Site')[0] ?? null;

        if ($secFetchSite === null) {
            return $handler->handle($request);
        }

        if (in_array($secFetchSite, ['same-origin', 'same-site', 'none'], true)) {
            return $handler->handle($request);
        }

        return $this->createForbiddenResponse($request);
    }

    private function createForbiddenResponse(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getHeader('Accept')[0] ?? '';

        if (str_contains($accept, 'application/json')) {
            return new JsonResponse([
                'success' => false,
                'error' => ['message' => 'Cross-site request detected'],
            ], 403);
        }

        return new TextResponse('Cross-site request detected', 403);
    }
}
