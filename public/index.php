<?php 
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
$settings = require __DIR__ . '/../app/settings.php';
$app = new \Slim\App($settings);
require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/middleware.php'; 
require __DIR__ . '/../app/app_loader.php';  
 
$app->run();