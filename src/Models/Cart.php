<?php

namespace App\Models;

class Cart
{
    public function addItem(int $productId, string $name, float $price, int $quantity = 1): void
    {
        if ($quantity <= 0) {
            return;
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $productId,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity
            ];
        }
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
    }

    public function removeItem(int $productId): void
    {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    public function getItems(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public function getCount(): int
    {
        $count = 0;
        foreach ($this->getItems() as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->getItems() as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return round($total, 2);
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public function isEmpty(): bool
    {
        return empty($_SESSION['cart']);
    }

    public function calculateLoyaltyPoints(): int
    {
        $total = $this->getTotal();
        return (int) floor($total / 100) * 10;
    }

    public static function calculatePointsForAmount(float $amount): int
    {
        return (int) floor($amount / 100) * 10;
    }
}
