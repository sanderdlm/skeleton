<?php

declare(strict_types=1);

namespace App\Test;

final class ApiControllerTest extends AbstractTestCase
{
    public function testApiStatusReturnsSuccess(): void
    {
        $response = $this->get('/api/status');

        static::assertSame(200, $response->getStatusCode());
    }

    public function testApiStatusReturnsJsonStructure(): void
    {
        $response = $this->get('/api/status');
        $json = $this->getJsonResponse($response);

        static::assertArrayHasKey('status', $json);
        static::assertArrayHasKey('data', $json);
        static::assertSame('success', $json['status']);
    }

    public function testApiStatusReturnsMessage(): void
    {
        $response = $this->get('/api/status');
        $json = $this->getJsonResponse($response);

        static::assertIsArray($json['data']);
        static::assertArrayHasKey('message', $json['data']);
        static::assertArrayHasKey('timestamp', $json['data']);
        static::assertSame('Hello from the API', $json['data']['message']);
        static::assertIsInt($json['data']['timestamp']);
    }

    public function testApiStatusHasCorrectContentType(): void
    {
        $response = $this->get('/api/status');

        static::assertSame(200, $response->getStatusCode());
        static::assertTrue($response->hasHeader('Content-Type'));
        static::assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));
    }
}
