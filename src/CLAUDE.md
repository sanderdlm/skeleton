# Agent context for PHP source code

## General
- Use immutable and final classes whenever possible: `final readonly class`
- Use constructor property promotion for autowired dependencies 
```php
// GOOD - Controllers inject autowired dependencies via constructor
public function __construct(
    private readonly SomeDependency $someDependency,
) {}
```
- Use type hints and strict types everywhere.
- No docblocks unless absolutely necessary.
- `declare(strict_types=1);` in all files
- PSR-12 coding standard
- Named parameters for clarity when useful
- Favor modern PHP features

## Controllers
- Must implement `ControllerInterface`
- Single `__invoke()` method
- Thin - just coordinate between repositories, services, and templates
- Return PSR-7 responses (HtmlResponse, RedirectResponse, JsonResponse)

