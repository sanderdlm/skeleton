<?php

declare(strict_types=1);

use App\Kernel;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap the application
$kernel = new Kernel(__DIR__ . '/..');

// Handle the request
$response = $kernel->dispatch(ServerRequestFactory::fromGlobals());

// 💨
(new SapiEmitter())->emit($response);
