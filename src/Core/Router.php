<?php

namespace App\Core;

class Router
{
    private $routes = [];

    public function add($path, $controller, $method)
    {
        $this->routes[$path] = [
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function dispatch($uri)
    {
        // Remove query string and trailing slashes
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        if (array_key_exists($uri, $this->routes)) {
            $controllerName = "App\\Controllers\\" . $this->routes[$uri]['controller'];
            $method = $this->routes[$uri]['method'];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $method)) {
                    return $controller->$method();
                }
            }
        }

        // Simple 404
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
}
