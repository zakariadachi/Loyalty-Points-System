<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    protected $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(dirname(__DIR__, 2) . '/templates');
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->twig->addGlobal('session', $_SESSION);

        // Calculate Base URL
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $baseUrl = ($scriptDir === '/') ? '' : $scriptDir;
        $this->twig->addGlobal('base_url', $baseUrl);
    }

    protected function render($template, $data = [])
    {
        echo $this->twig->render($template, $data);
    }
}
