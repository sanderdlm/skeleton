<?php

declare(strict_types=1);

use App\Service\DatabaseConnectionFactory;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;

require_once __DIR__ . '/vendor/autoload.php';

$config = new PhpFile(__DIR__ . '/migrations.php');
$connection = DatabaseConnectionFactory::create('dev', __DIR__);

return DependencyFactory::fromConnection($config, new ExistingConnection($connection));
