<?php

declare(strict_types=1);

namespace App\Test;

use App\Kernel;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractTestCase extends TestCase
{
    protected Kernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = new Kernel(__DIR__ . '/..');
    }

    /**
     * @param array<non-empty-string, array<array-key, string>|string> $headers
     */
    protected function get(string $uri, array $headers = []): ResponseInterface
    {
        return $this->request('GET', $uri, headers: $headers);
    }

    /**
     * @param array<non-empty-string, array<array-key, string>|string> $body
     * @param array<non-empty-string, array<array-key, string>|string> $headers
     */
    protected function post(string $uri, array $body = [], array $headers = []): ResponseInterface
    {
        return $this->request('POST', $uri, body: $body, headers: $headers);
    }

    /**
     * @param array<non-empty-string, array<array-key, string>|string> $body
     * @param array<non-empty-string, array<array-key, string>|string> $headers
     */
    protected function request(string $method, string $uri, array $body = [], array $headers = []): ResponseInterface
    {
        $request = new ServerRequest(
            serverParams: [],
            uploadedFiles: [],
            uri: $uri,
            method: $method,
            body: 'php://memory',
            headers: $headers,
        );

        if ($body !== []) {
            $request = $request->withParsedBody($body);
        }

        return $this->kernel->dispatch($request);
    }

    protected function assertSelectorExists(ResponseInterface $response, string $selector): void
    {
        $crawler = new Crawler((string) $response->getBody());
        $this->assertGreaterThan(
            0,
            $crawler->filter($selector)->count(),
            sprintf("Selector '%s' not found in response", $selector),
        );
    }

    protected function assertSelectorTextContains(ResponseInterface $response, string $selector, string $text): void
    {
        $crawler = new Crawler((string) $response->getBody());
        $element = $crawler->filter($selector);

        $this->assertGreaterThan(0, $element->count(), sprintf("Selector '%s' not found in response", $selector));
        $this->assertStringContainsString($text, $element->text());
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function getJsonResponse(ResponseInterface $response): array
    {
        $decoded = json_decode((string) $response->getBody(), true);
        $this->assertIsArray($decoded, 'Response body is not valid JSON array');
        return $decoded;
    }
}
