<?php

namespace App\Models;

use App\Core\Database;

class PurchaseOrder
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT po.*, s.name as supplier_name 
            FROM purchase_orders po 
            JOIN suppliers s ON po.supplier_id = s.id 
            ORDER BY po.id DESC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT po.*, s.name as supplier_name, s.email as supplier_email, s.phone as supplier_phone
            FROM purchase_orders po 
            JOIN suppliers s ON po.supplier_id = s.id 
            WHERE po.id = ?
        ");
        $stmt->execute([$id]);
        $po = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$po) {
            return null;
        }

        // Fetch items
        $iStmt = $db->prepare("
            SELECT poi.*, pv.sku, p.name as product_name, pv.attributes
            FROM purchase_order_items poi
            JOIN product_variants pv ON poi.variant_id = pv.id
            JOIN products p ON pv.product_id = p.id
            WHERE poi.purchase_order_id = ?
            ORDER BY poi.id ASC
        ");
        $iStmt->execute([$id]);
        $po['items'] = $iStmt->fetchAll(\PDO::FETCH_ASSOC);

        return $po;
    }

    public static function create(int $supplierId, array $items): int
    {
        $db = Database::getConnection();
        
        $db->beginTransaction();
        try {
            // Calculate total cost
            $totalCost = 0;
            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 0);
                $cost = (float) ($item['unit_cost'] ?? 0);
                $totalCost += $qty * $cost;
            }

            // Insert PO
            $stmt = $db->prepare("
                INSERT INTO purchase_orders (supplier_id, status, total_cost) 
                VALUES (?, 'draft', ?)
            ");
            $stmt->execute([$supplierId, $totalCost]);
            $poId = (int) $db->lastInsertId();

            // Insert items
            $iStmt = $db->prepare("
                INSERT INTO purchase_order_items (purchase_order_id, variant_id, quantity, unit_cost) 
                VALUES (?, ?, ?, ?)
            ");
            foreach ($items as $item) {
                $iStmt->execute([
                    $poId,
                    (int) ($item['variant_id'] ?? 0),
                    (int) ($item['quantity'] ?? 0),
                    (float) ($item['unit_cost'] ?? 0)
                ]);
            }

            $db->commit();
            return $poId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function updateStatus(int $id, string $status): bool
    {
        $db = Database::getConnection();
        
        // Fetch current PO status
        $po = self::find($id);
        if (!$po) {
            return false;
        }

        $currentStatus = $po['status'];
        if ($currentStatus === $status) {
            return true;
        }

        // Only transition received if not already received
        if ($currentStatus === 'received') {
            throw new \Exception("Cannot change status of an already received Purchase Order.");
        }

        $db->beginTransaction();
        try {
            if ($status === 'received') {
                // Set received date
                $stmt = $db->prepare("
                    UPDATE purchase_orders 
                    SET status = ?, received_at = CURRENT_TIMESTAMP 
                    WHERE id = ?
                ");
                $stmt->execute([$status, $id]);

                // Increment stock quantities of each variant
                $stockStmt = $db->prepare("
                    UPDATE product_variants 
                    SET stock_qty = stock_qty + ? 
                    WHERE id = ?
                ");
                foreach ($po['items'] as $item) {
                    $stockStmt->execute([
                        (int) $item['quantity'],
                        (int) $item['variant_id']
                    ]);
                }
            } else {
                $stmt = $db->prepare("
                    UPDATE purchase_orders 
                    SET status = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$status, $id]);
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        
        $po = self::find($id);
        if (!$po) {
            return false;
        }

        // Only allow delete if draft or cancelled
        if ($po['status'] !== 'draft' && $po['status'] !== 'cancelled') {
            throw new \Exception("Only draft or cancelled purchase orders can be deleted.");
        }

        $stmt = $db->prepare("DELETE FROM purchase_orders WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
