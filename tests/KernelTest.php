<?php

declare(strict_types=1);

namespace App\Test;

final class KernelTest extends AbstractTestCase
{
    public function testKernelCanDispatch(): void
    {
        $response = $this->get('/');

        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('🤖', (string) $response->getBody());
        static::assertSelectorExists($response, 'p');
        static::assertSelectorTextContains($response, 'p', '🤖');
    }
}
