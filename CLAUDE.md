# Agent project context

## General
- This app is in active development. No need for backwards compatibility in the code. Make breaking changes and remove outdated code paths as needed.
- Always check your work by running the checks:
```bash
composer check    # Full QA suite
composer test     # PHPUnit only
composer analyze  # Static analysis
composer lint    # Code style check
composer fix   # Auto-fix style issues
composer coverage   # PHPUnit coverage report
```
- Don't add comments unless a piece of code absolutely requires it.

### Templates
- All extend `base.twig`
- Reusable components in `templates/components/`
- Pages in `templates/pages/`
- No inline styles - use CSS classes from `public/css/style.css`




