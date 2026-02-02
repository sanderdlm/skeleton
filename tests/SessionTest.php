<?php

declare(strict_types=1);

namespace App\Test;

use PSR7Sessions\Storageless\Http\SessionMiddleware;

final class SessionTest extends AbstractTestCase
{
    public function testSessionMiddlewareIsConfigured(): void
    {
        $response = $this->get('/session');

        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testSessionCounterIncrements(): void
    {
        $response1 = $this->get('/session');
        static::assertSame(200, $response1->getStatusCode());

        $data1 = $this->getJsonResponse($response1);
        static::assertArrayHasKey('counter', $data1);
        static::assertSame(1, $data1['counter']);
    }

    public function testSessionPersistsAcrossRequests(): void
    {
        $response1 = $this->get('/session');
        $data1 = $this->getJsonResponse($response1);
        static::assertSame(1, $data1['counter']);

        $setCookieHeader = $response1->getHeaderLine('Set-Cookie');
        static::assertNotEmpty($setCookieHeader, 'Session cookie should be set');

        preg_match('/slsession=([^;]+)/', $setCookieHeader, $matches);
        static::assertNotEmpty($matches, 'Session cookie value should be present');

        $sessionCookie = $matches[1];
        
        $response2 = $this->get('/session', ['Cookie' => 'slsession=' . $sessionCookie]);
        $data2 = $this->getJsonResponse($response2);
        static::assertSame(2, $data2['counter'], 'Counter should increment when session cookie is sent');

        $response3 = $this->get('/session', ['Cookie' => 'slsession=' . $sessionCookie]);
        $data3 = $this->getJsonResponse($response3);
        static::assertSame(3, $data3['counter'], 'Counter should continue incrementing');
    }

    public function testSessionCookieIsSet(): void
    {
        $response = $this->get('/session');

        $setCookieHeader = $response->getHeaderLine('Set-Cookie');
        static::assertStringContainsString('slsession=', $setCookieHeader);
        static::assertStringContainsString('HttpOnly', $setCookieHeader);
        static::assertStringContainsString('Path=/', $setCookieHeader);
    }

    public function testNewSessionStartsWithCounterOne(): void
    {
        $response = $this->get('/session');

        $data = $this->getJsonResponse($response);
        static::assertSame(1, $data['counter']);
        static::assertSame('Session counter incremented', $data['message']);
    }
}
