<?php

declare(strict_types=1);

namespace App\Test;

final class HomeControllerTest extends BaseTestCase
{
    public function testHomePage(): void
    {
        $response = $this->get('/');

        $this->assertResponseOk($response);
        $this->assertSelectorTextContains($response, 'title', 'Home');
        $this->assertResponseContains($response, 'Welcome');
    }

    public function testHomePageHasCorrectStructure(): void
    {
        $response = $this->get('/');

        $this->assertResponseOk($response);
        $this->assertSelectorExists($response, 'html');
        $this->assertSelectorExists($response, 'body');
        $this->assertSelectorExists($response, 'title');
    }
}
