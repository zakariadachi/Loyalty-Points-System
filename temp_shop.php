<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;

class ShopController extends Controller
{
    private Cart $cart;
    private array $products;

    public function __construct()
    {
        parent::__construct();
        $this->cart = new Cart();
        
        $this->products = [
            1 => [
                'id' => 1,
                'name' => 'Smartphone Premium',
                'price' => 799.99,
                'description' => 'Dernier modèle avec écran OLED et caméra 108MP',
                'icon' => 'SP'
            ],
            2 => [
                'id' => 2,
                'name' => 'Casque Bluetooth',
                'price' => 129.99,
                'description' => 'Son haute fidélité avec réduction de bruit active',
                'icon' => 'CB'
            ],
            3 => [
                'id' => 3,
                'name' => 'Livre PHP',
                'price' => 49.99,
                'description' => 'Maîtrisez PHP 8 et les design patterns MVC',
                'icon' => 'LP'
            ],
            4 => [
                'id' => 4,
                'name' => 'T-shirt ShopEasy',
                'price' => 24.99,
                'description' => 'T-shirt 100% coton bio avec logo ShopEasy',
                'icon' => 'TS'
            ],
            5 => [
                'id' => 5,
                'name' => 'Sac à dos',
                'price' => 59.99,
                'description' => 'Sac à dos ergonomique avec compartiment laptop',
                'icon' => 'SD'
            ],
            6 => [
                'id' => 6,
                'name' => 'Tablette graphique',
                'price' => 299.99,
                'description' => 'Tablette professionnelle pour designers',
                'icon' => 'TG'
            ]
        ];
    }

    private function getCommonData(): array
    {
        return [
            'user' => $_SESSION['user'] ?? null
        ];
    }

    public function index(): void
    {
        $this->render('shop/index.html.twig', array_merge(
            $this->getCommonData(),
            ['products' => $this->products]
        ));
    }

    public function cart(): void
    {
        $this->render('shop/cart.html.twig', array_merge(
            $this->getCommonData(),
            [
                'cart_items' => $this->cart->getItems(),
                'cart_total' => $this->cart->getTotal(),
                'loyalty_points' => $this->cart->calculateLoyaltyPoints()
            ]
        ));
    }

    public function addToCart(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop');
            exit;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if (isset($this->products[$productId]) && $quantity > 0) {
            $product = $this->products[$productId];
            $this->cart->addItem(
                $product['id'],
                $product['name'],
                $product['price'],
                $quantity
            );
        }

        header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/cart');
        exit;
    }

    public function updateCart(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/cart');
            exit;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);

        $this->cart->updateQuantity($productId, $quantity);

        header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/cart');
        exit;
    }

    public function removeFromCart(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/cart');
            exit;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $this->cart->removeItem($productId);

        header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/cart');
        exit;
    }

    public function checkout(): void
    {
        if ($this->cart->isEmpty()) {
            header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/cart');
            exit;
        }

        $checkoutError = $_SESSION['checkout_error'] ?? null;
        unset($_SESSION['checkout_error']);

        $this->render('shop/checkout.html.twig', array_merge(
            $this->getCommonData(),
            [
                'cart_items' => $this->cart->getItems(),
                'cart_total' => $this->cart->getTotal(),
                'loyalty_points' => $this->cart->calculateLoyaltyPoints(),
                'checkout_error' => $checkoutError
            ]
        ));
    }

    public function processCheckout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/checkout');
            exit;
        }

        if ($this->cart->isEmpty()) {
            header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/cart');
            exit;
        }

        $cardName = trim($_POST['card_name'] ?? '');
        $cardNumber = trim($_POST['card_number'] ?? '');
        $cardExpiry = trim($_POST['card_expiry'] ?? '');
        $cardCvv = trim($_POST['card_cvv'] ?? '');

        if (empty($cardName) || empty($cardNumber) || empty($cardExpiry) || empty($cardCvv)) {
            $_SESSION['checkout_error'] = 'Veuillez remplir tous les champs.';
            header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/checkout');
            exit;
        }

        $cartTotal = $this->cart->getTotal();
        $pointsEarned = $this->cart->calculateLoyaltyPoints();
        
        // Handle guest or registered user
        if (isset($_SESSION['user'])) {
            $_SESSION['user']['loyalty_points'] += $pointsEarned;
            $newPoints = $_SESSION['user']['loyalty_points'];
            $previousPoints = $newPoints - $pointsEarned;
        } else {
            $previousPoints = 0;
            $newPoints = $pointsEarned;
        }

        $_SESSION['last_purchase'] = [
            'items' => $this->cart->getItems(),
            'total' => $cartTotal,
            'points_earned' => $pointsEarned,
            'previous_points' => $previousPoints,
            'new_points' => $newPoints,
            'date' => date('Y-m-d H:i:s')
        ];

        $this->cart->clear();

        header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop/purchase-result');
        exit;
    }

    public function purchaseResult(): void
    {
        if (!isset($_SESSION['last_purchase'])) {
            header('Location: ' . $this->twig->getGlobals()['base_url'] . '/shop');
            exit;
        }

        $this->render('shop/purchase_result.html.twig', array_merge(
            $this->getCommonData(),
            ['purchase' => $_SESSION['last_purchase']]
        ));
    }
}

