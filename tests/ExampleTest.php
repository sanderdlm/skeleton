<?php

namespace App\Test;

class ExampleTest extends BaseTestCase
{
    public function testExample(): void
    {
        // Arrange
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        // Act
        $output = $this->getApplicationOutput();

        // Assert
        $this->assertHasTagWithContent('h1', 'Home', $output);
    }
}
