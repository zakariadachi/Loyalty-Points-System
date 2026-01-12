<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Router;

$router = new Router();

// Define routes
$router->add('/', 'HomeController', 'index');

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI']);
