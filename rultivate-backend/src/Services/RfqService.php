<?php
namespace Rultivate\Services;

use Rultivate\Models\Rfq;
use Rultivate\Models\RfqInvite;
use Rultivate\Models\Bid;
use Rultivate\Models\Vendor;
use Rultivate\Utils\Sanitizer;

class RfqService extends BaseService
{
    public function create(int $customerId, array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $stmt = $this->db->prepare('INSERT INTO ' . Rfq::TABLE . ' (customer_id, title, description, category, budget_inr, expected_delivery_date, status, created_at, updated_at) VALUES (:customer_id, :title, :description, :category, :budget_inr, :expected_delivery_date, :status, NOW(), NOW())');
        $stmt->execute([
            'customer_id' => $customerId,
            'title' => $clean['title'],
            'description' => $clean['description'],
            'category' => $clean['category'],
            'budget_inr' => $clean['budgetInr'] ?? null,
            'expected_delivery_date' => $clean['expectedDeliveryDate'] ?? null,
            'status' => 'PUBLISHED'
        ]);
        $id = (int)$this->db->lastInsertId();
        return $this->find($id) ?? [];
    }

    public function update(int $id, int $customerId, array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $stmt = $this->db->prepare('UPDATE ' . Rfq::TABLE . ' SET title = :title, description = :description, category = :category, budget_inr = :budget_inr, expected_delivery_date = :expected_delivery_date, status = :status, updated_at = NOW() WHERE id = :id AND customer_id = :customer_id');
        $stmt->execute([
            'title' => $clean['title'] ?? null,
            'description' => $clean['description'] ?? null,
            'category' => $clean['category'] ?? null,
            'budget_inr' => $clean['budgetInr'] ?? null,
            'expected_delivery_date' => $clean['expectedDeliveryDate'] ?? null,
            'status' => $clean['status'] ?? 'PUBLISHED',
            'id' => $id,
            'customer_id' => $customerId
        ]);
        return $this->find($id) ?? [];
    }

    public function delete(int $id, int $customerId): void
    {
        $stmt = $this->db->prepare('DELETE FROM ' . Rfq::TABLE . ' WHERE id = :id AND customer_id = :customer_id');
        $stmt->execute(['id' => $id, 'customer_id' => $customerId]);
    }

    public function list(int $customerId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Rfq::TABLE . ' WHERE customer_id = :customer_id ORDER BY created_at DESC');
        $stmt->execute(['customer_id' => $customerId]);
        return $stmt->fetchAll();
    }

    public function listForVendor(int $vendorId): array
    {
        $stmt = $this->db->prepare('SELECT r.* FROM ' . Rfq::TABLE . ' r LEFT JOIN ' . RfqInvite::TABLE . ' ri ON ri.rfq_id = r.id WHERE r.status = "PUBLISHED" AND (ri.vendor_id = :vendor_id OR ri.vendor_id IS NULL)');
        $stmt->execute(['vendor_id' => $vendorId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Rfq::TABLE . ' WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function listBids(int $rfqId): array
    {
        $stmt = $this->db->prepare('SELECT b.*, v.company_name FROM ' . Bid::TABLE . ' b INNER JOIN ' . Vendor::TABLE . ' v ON v.id = b.vendor_id WHERE b.rfq_id = :rfq_id');
        $stmt->execute(['rfq_id' => $rfqId]);
        return $stmt->fetchAll();
    }

    public function invite(int $rfqId, array $vendorIds): void
    {
        foreach ($vendorIds as $vendorId) {
            $stmt = $this->db->prepare('INSERT INTO ' . RfqInvite::TABLE . ' (rfq_id, vendor_id, status, created_at, updated_at) VALUES (:rfq_id, :vendor_id, :status, NOW(), NOW()) ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = NOW()');
            $stmt->execute([
                'rfq_id' => $rfqId,
                'vendor_id' => $vendorId,
                'status' => 'INVITED'
            ]);
        }
    }
}
