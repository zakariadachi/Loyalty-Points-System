<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->render('home/index.html.twig', [
            'title' => 'ShopEasy - Système de Fidélité',
            'welcome_message' => 'Bienvenue sur votre espace de fidélité ShopEasy !'
        ]);
    }
}
