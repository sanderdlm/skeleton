<?php

declare(strict_types=1);

use App\Service\DatabaseConnectionFactory;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();

$config = new PhpFile(__DIR__ . '/migrations.php');
$environment = $_ENV['APP_ENV'] ?? 'prod';
$connection = DatabaseConnectionFactory::create($environment, __DIR__);

return DependencyFactory::fromConnection($config, new ExistingConnection($connection));
