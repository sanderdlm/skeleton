# PHP + HTMX = ❤️

Opinionated, modern PHP web application skeleton using Twig templates and HTMX for interactivity.

* [PSR-15](https://www.php-fig.org/psr/psr-15/) application architecture
* [nikic/FastRoute](https://github.com/nikic/FastRoute) for routing
* [PHP-DI](https://github.com/PHP-DI/PHP-DI) for the [PSR-11](https://www.php-fig.org/psr/psr-11/) container
* [laminas/laminas-diactoros](https://github.com/laminas/laminas-diactoros) for the [PSR-7](https://www.php-fig.org/psr/psr-7/) implementation
* [twigphp/twig](https://twig.symfony.com/) for templates
* [HTMX](https://htmx.org/) for interactivity
* [middlewares/psr15-middlewares](https://github.com/middlewares/psr15-middlewares) for some common middlewares (error handling, routing & request handling)

## Create project
```bash
composer create-project sanderdlm/skeleton <your-project-name>
```
## Run project
```bash
composer start
```
Find your project at [http://localhost:8080](http://localhost:8080)