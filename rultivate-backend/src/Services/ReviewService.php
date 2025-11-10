<?php
namespace Rultivate\Services;

use Rultivate\Models\Review;
use Rultivate\Utils\Sanitizer;

class ReviewService extends BaseService
{
    public function create(int $orderId, int $customerId, array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $stmt = $this->db->prepare('INSERT INTO ' . Review::TABLE . ' (order_id, vendor_id, customer_id, rating, comment, is_hidden, created_at, updated_at) VALUES (:order_id, :vendor_id, :customer_id, :rating, :comment, 0, NOW(), NOW())');
        $stmt->execute([
            'order_id' => $orderId,
            'vendor_id' => $clean['vendorId'],
            'customer_id' => $customerId,
            'rating' => $clean['rating'],
            'comment' => $clean['comment'] ?? null
        ]);
        return $this->find((int)$this->db->lastInsertId()) ?? [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Review::TABLE . ' WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function listByVendor(int $vendorId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Review::TABLE . ' WHERE vendor_id = :vendor_id AND is_hidden = 0 ORDER BY created_at DESC');
        $stmt->execute(['vendor_id' => $vendorId]);
        return $stmt->fetchAll();
    }

    public function hide(int $reviewId): void
    {
        $this->db->prepare('UPDATE ' . Review::TABLE . ' SET is_hidden = 1 WHERE id = :id')->execute(['id' => $reviewId]);
    }
}
