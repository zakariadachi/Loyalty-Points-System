<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Router;

$router = new Router();

// Load routes
$routes = require_once dirname(__DIR__) . '/config/routes.php';
foreach ($routes as $path => $handler) {
    $router->add($path, $handler[0], $handler[1]);
}

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI']);
