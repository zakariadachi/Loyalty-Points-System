<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Purchase
{
    private $db;
    private $table = 'purchases';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crée un nouvel achat
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (user_id, total_amount, status, created_at) 
                VALUES (:user_id, :total_amount, :status, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':total_amount' => $data['total_amount'],
            ':status' => $data['status']
        ]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Récupère les achats d'un utilisateur
     */
    public function getUserPurchases(int $userId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
