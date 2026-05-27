<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class BodyParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            $body = [];
            parse_str((string) $request->getBody(), $body);
            $request = $request->withParsedBody($body);
        } elseif (str_contains($contentType, 'application/json')) {
            $body = json_decode((string) $request->getBody(), true);
            if (is_array($body)) {
                $request = $request->withParsedBody($body);
            }
        }

        return $handler->handle($request);
    }
}
