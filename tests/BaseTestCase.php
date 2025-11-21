<?php

declare(strict_types=1);

namespace App\Test;

use App\Kernel;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseTestCase extends TestCase
{
    protected Kernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = new Kernel(__DIR__ . '/..');
    }

    protected function get(string $uri, array $headers = []): ResponseInterface
    {
        return $this->request('GET', $uri, headers: $headers);
    }

    protected function post(string $uri, array $body = [], array $headers = []): ResponseInterface
    {
        return $this->request('POST', $uri, body: $body, headers: $headers);
    }

    protected function request(
        string $method,
        string $uri,
        array $body = [],
        array $headers = []
    ): ResponseInterface {
        $request = new ServerRequest(
            serverParams: [],
            uploadedFiles: [],
            uri: $uri,
            method: $method,
            body: 'php://memory',
            headers: $headers
        );

        if ($body !== []) {
            $request = $request->withParsedBody($body);
        }

        return $this->kernel->dispatch($request);
    }

    protected function assertResponseOk(ResponseInterface $response): void
    {
        $this->assertSame(200, $response->getStatusCode());
    }

    protected function assertResponseStatus(ResponseInterface $response, int $expectedStatus): void
    {
        $this->assertSame($expectedStatus, $response->getStatusCode());
    }

    protected function assertResponseContains(ResponseInterface $response, string $needle): void
    {
        $body = (string) $response->getBody();
        $this->assertStringContainsString($needle, $body);
    }

    protected function assertSelectorExists(ResponseInterface $response, string $selector): void
    {
        $crawler = new Crawler((string) $response->getBody());
        $this->assertGreaterThan(
            0,
            $crawler->filter($selector)->count(),
            "Selector '$selector' not found in response"
        );
    }

    protected function assertSelectorTextContains(ResponseInterface $response, string $selector, string $text): void
    {
        $crawler = new Crawler((string) $response->getBody());
        $element = $crawler->filter($selector);

        $this->assertGreaterThan(
            0,
            $element->count(),
            "Selector '$selector' not found in response"
        );
        $this->assertStringContainsString($text, $element->text());
    }

    protected function getResponseBody(ResponseInterface $response): string
    {
        return (string) $response->getBody();
    }

    protected function getJsonResponse(ResponseInterface $response): array
    {
        $decoded = json_decode($this->getResponseBody($response), true);
        $this->assertIsArray($decoded, 'Response is not valid JSON');
        return $decoded;
    }
}
