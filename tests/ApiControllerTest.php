<?php

declare(strict_types=1);

namespace App\Test;

final class ApiControllerTest extends BaseTestCase
{
    public function testApiStatusReturnsSuccess(): void
    {
        $response = $this->get('/api/status');

        $this->assertResponseOk($response);
    }

    public function testApiStatusReturnsJsonStructure(): void
    {
        $response = $this->get('/api/status');
        $json = $this->getJsonResponse($response);

        $this->assertArrayHasKey('status', $json);
        $this->assertArrayHasKey('data', $json);
        $this->assertSame('success', $json['status']);
    }

    public function testApiStatusReturnsMessage(): void
    {
        $response = $this->get('/api/status');
        $json = $this->getJsonResponse($response);

        $this->assertIsArray($json['data']);
        $this->assertArrayHasKey('message', $json['data']);
        $this->assertArrayHasKey('timestamp', $json['data']);
        $this->assertSame('Hello from the API', $json['data']['message']);
        $this->assertIsInt($json['data']['timestamp']);
    }

    public function testApiStatusHasCorrectContentType(): void
    {
        $response = $this->get('/api/status');

        $this->assertResponseOk($response);
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));
    }
}
