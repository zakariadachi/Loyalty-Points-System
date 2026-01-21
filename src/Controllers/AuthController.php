<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function login()
    {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);
            if ($user && $this->userModel->verifyPassword($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header('Location: ' . $this->twig->getGlobals()['base_url'] . '/');
                exit;
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }

        $this->render('auth/login.html.twig', [
            'title' => 'Connexion - ShopEasy',
            'error' => $error
        ]);
    }

    public function register()
    {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->userModel->findByEmail($email)) {
                $error = "Cet email est déjà utilisé.";
            } else {
                if ($this->userModel->save($name, $email, $password)) {
                    header('Location: ' . $this->twig->getGlobals()['base_url'] . '/login');
                    exit;
                } else {
                    $error = "Une erreur est survenue lors de l'inscription.";
                }
            }
        }

        $this->render('auth/register.html.twig', [
            'title' => 'Inscription - ShopEasy',
            'error' => $error
        ]);
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . $this->twig->getGlobals()['base_url'] . '/');
        exit;
    }
}
