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
        // Decode URI (important for spaces %20)
        $uri = urldecode(parse_url($uri, PHP_URL_PATH));
        
        // Get the directory where index.php is located
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        
        // Strip the script directory from the URI
        if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

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

        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
}
