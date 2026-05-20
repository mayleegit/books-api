<?php 
use Slim\Factory\AppFactory; 

require __DIR__ . '/../vendor/autoload.php'; 
  
$app = AppFactory::create(); 
$app->add(new App\Middleware\JsonBodyParser());   // ← Step 13 
$app->add(new App\Middleware\Cors());             // ← Step 14 
$app->addRoutingMiddleware(); 
$app->addErrorMiddleware(true, true, true); 

(require __DIR__ . '/../src/routes.php')($app); 
  
$app->run();