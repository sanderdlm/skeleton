<?php

declare(strict_types=1);

namespace App\Test;

final class HomeControllerTest extends AbstractTestCase
{
    public function testHomePage(): void
    {
        $response = $this->get('/');

        static::assertSame(200, $response->getStatusCode());
        $this->assertSelectorTextContains($response, 'title', 'Home');
        static::assertStringContainsString('Welcome', (string) $response->getBody());
    }

    public function testHomePageHasCorrectStructure(): void
    {
        $response = $this->get('/');

        static::assertSame(200, $response->getStatusCode());
        $this->assertSelectorExists($response, 'html');
        $this->assertSelectorExists($response, 'body');
        $this->assertSelectorExists($response, 'title');
    }
}
