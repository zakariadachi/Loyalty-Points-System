<?php

return [
    '/' => ['HomeController', 'index'],
    '/register' => ['AuthController', 'register'],
    '/login' => ['AuthController', 'login'],
    '/logout' => ['AuthController', 'logout'],
    '/shop' => ['ShopController', 'index'],
    '/shop/cart' => ['ShopController', 'cart'],
    '/shop/add' => ['ShopController', 'addToCart'],
    '/shop/update' => ['ShopController', 'updateCart'],
    '/shop/remove' => ['ShopController', 'removeFromCart'],
    '/shop/checkout' => ['ShopController', 'checkout'],
    '/shop/process-checkout' => ['ShopController', 'processCheckout'],
    '/shop/purchase-result' => ['ShopController', 'purchaseResult'],
];
