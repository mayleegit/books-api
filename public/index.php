<?php

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

$app = AppFactory::create();

// Added first so it runs last on normal responses.
$app->add(new App\Middleware\SecurityHeaders());
$app->add(new App\Middleware\JsonBodyParser());
$app->addRoutingMiddleware();
$app->add(new App\Middleware\Cors());
$app->addErrorMiddleware(true, true, true);

(require __DIR__ . '/../src/routes.php')($app);

$app->run();
