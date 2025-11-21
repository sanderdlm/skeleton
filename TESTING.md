# Testing Guide

## Overview

This app uses a simple, direct PSR-7 testing approach without complex abstraction layers.

## Writing Tests

All test cases extend `App\Test\BaseTestCase` which provides a clean API for testing.

### Basic HTTP Testing

```php
final class MyFeatureTest extends BaseTestCase
{
    public function testGetEndpoint(): void
    {
        // Make a GET request
        $response = $this->get('/some-path');

        // Assert response status
        $this->assertResponseOk($response);  // 200
        $this->assertResponseStatus($response, 404);  // or any status code

        // Assert response content
        $this->assertResponseContains($response, 'Some text');
    }

    public function testPostEndpoint(): void
    {
        // Make a POST request with body
        $response = $this->post('/api/endpoint', [
            'key' => 'value',
        ]);

        $this->assertResponseOk($response);
    }
}
```

### HTML/DOM Testing

For server-side rendered templates:

```php
public function testPageStructure(): void
{
    $response = $this->get('/');

    // Check if selector exists
    $this->assertSelectorExists($response, 'h1');
    $this->assertSelectorExists($response, '.hero');

    // Check selector text content
    $this->assertSelectorTextContains($response, 'title', 'Home');
    $this->assertSelectorTextContains($response, 'h1', 'Welcome');
}
```

### JSON API Testing

For JSON endpoints:

```php
public function testJsonApi(): void
{
    $response = $this->post('/api/data', ['input' => 'test']);

    $this->assertResponseOk($response);

    $json = $this->getJsonResponse($response);
    $this->assertArrayHasKey('status', $json);
    $this->assertSame('success', $json['status']);
}
```

### Advanced Requests

```php
// Custom headers
$response = $this->get('/api/endpoint', [
    'Authorization' => 'Bearer token123',
    'Accept' => 'application/json',
]);

// Custom HTTP methods
$response = $this->request('PATCH', '/api/resource/123',
    body: ['status' => 'updated'],
    headers: ['Content-Type' => 'application/json']
);

// Access raw response body
$body = $this->getResponseBody($response);

// Direct access to PSR-7 response
$statusCode = $response->getStatusCode();
$headers = $response->getHeaders();
```

## Available Assertions

- `assertResponseOk(ResponseInterface $response)` - Assert 200 status
- `assertResponseStatus(ResponseInterface $response, int $code)` - Assert specific status code
- `assertResponseContains(ResponseInterface $response, string $text)` - Assert body contains text
- `assertSelectorExists(ResponseInterface $response, string $selector)` - Assert CSS selector exists
- `assertSelectorTextContains(ResponseInterface $response, string $selector, string $text)` - Assert selector contains text

## Helper Methods

- `get(string $uri, array $headers = []): ResponseInterface` - Make GET request
- `post(string $uri, array $body = [], array $headers = []): ResponseInterface` - Make POST request
- `request(string $method, string $uri, array $body = [], array $headers = []): ResponseInterface` - Make any HTTP request
- `getResponseBody(ResponseInterface $response): string` - Get raw response body as string
- `getJsonResponse(ResponseInterface $response): array` - Parse and return JSON response as array

## Running Tests

```bash
composer test           # Run PHPUnit only
composer check          # Run full QA suite (tests + phpstan + phpcs)
composer phpstan        # Static analysis
composer phpcs          # Code style check
composer phpcbf         # Auto-fix style issues
```