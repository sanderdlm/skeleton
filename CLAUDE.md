# Claude Code Context

## Rules

### General
- Use immutable and final classes whenever possible: `final readonly class`
- Use constructor property promotion for autowired dependencies 
```php
// GOOD - Controllers inject autowired dependencies via constructor
public function __construct(
    private readonly SomeDependency $someDependency,
) {}
```
- This app is in active development. No need for backwards compatibility in the code. Make breaking changes and remove outdated code paths as needed.
- Always check your work by running the checks:
```bash
composer check    # Full QA suite
composer test     # PHPUnit only
composer phpstan  # Static analysis
composer phpcs    # Code style check
composer phpcbf   # Auto-fix style issues
```
- Use type hints and strict types everywhere.
- No docblocks unless absolutely necessary.
- `declare(strict_types=1);` in all files
- PSR-12 coding standard
- Named parameters for clarity when useful
- Favor modern PHP features

### Controllers

- Must implement `ControllerInterface`
- Single `__invoke()` method
- Thin - just coordinate between repositories, services, and templates
- Return PSR-7 responses (HtmlResponse, RedirectResponse, JsonResponse)

### Templates

- All extend `base.twig`
- Reusable components in `templates/components/`
- Pages in `templates/pages/`
- No inline styles - use CSS classes from `public/css/style.css`




