<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ProductReview
{
    public static function findByProductId(int $productId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, c.name as customer_name
            FROM product_reviews r
            JOIN customers c ON r.customer_id = c.id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAverageRating(int $productId): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT COUNT(*) as total_reviews, AVG(rating) as average_rating
            FROM product_reviews
            WHERE product_id = ?
        ");
        $stmt->execute([$productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && $row['total_reviews'] > 0) {
            return [
                'total_reviews' => (int)$row['total_reviews'],
                'average_rating' => round((float)$row['average_rating'], 1)
            ];
        }
        
        return [
            'total_reviews' => 0,
            'average_rating' => 0.0
        ];
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO product_reviews (product_id, customer_id, rating, comment)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['product_id'],
            $data['customer_id'],
            $data['rating'],
            $data['comment'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }
}
