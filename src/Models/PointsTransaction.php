<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class PointsTransaction
{
    private $db;
    const POINTS_PER_EURO = 0.1;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function calculatePoints(float $amount): int
    {
        return floor($amount * self::POINTS_PER_EURO * 10) / 10 * 10;
        return floor($amount / 10); 
    }

    public function addTransaction($userId, $amount, $description, $type = 'earn')
    {
        $points = ($type === 'earn') ? $this->calculatePoints($amount) : -$amount;
        
        $sql = "INSERT INTO points_transactions (user_id, amount, points, type, description) 
                VALUES (:user_id, :amount, :points, :type, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'amount' => $amount,
            'points' => $points,
            'type' => $type,
            'description' => $description
        ]);
    }

    public function getUserPoints($userId)
    {
        $sql = "SELECT SUM(points) as total FROM points_transactions WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    public function getHistory($userId)
    {
        $sql = "SELECT * FROM points_transactions WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
