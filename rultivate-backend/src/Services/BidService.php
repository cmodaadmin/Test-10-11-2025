<?php
namespace Rultivate\Services;

use Rultivate\Models\Bid;
use Rultivate\Models\Order;
use Rultivate\Models\OrderItem;
use Rultivate\Models\Rfq;
use Rultivate\Utils\Database;

class BidService extends BaseService
{
    private OrderService $orderService;

    public function __construct(Database $database, OrderService $orderService)
    {
        parent::__construct($database);
        $this->orderService = $orderService;
    }

    public function submit(int $rfqId, int $vendorId, array $payload): array
    {
        $stmt = $this->db->prepare('INSERT INTO ' . Bid::TABLE . ' (rfq_id, vendor_id, amount_inr, delivery_timeline_days, notes, status, created_at, updated_at) VALUES (:rfq_id, :vendor_id, :amount_inr, :delivery_timeline_days, :notes, :status, NOW(), NOW()) ON DUPLICATE KEY UPDATE amount_inr = VALUES(amount_inr), delivery_timeline_days = VALUES(delivery_timeline_days), notes = VALUES(notes), status = "UPDATED", updated_at = NOW()');
        $stmt->execute([
            'rfq_id' => $rfqId,
            'vendor_id' => $vendorId,
            'amount_inr' => $payload['amountInr'],
            'delivery_timeline_days' => $payload['deliveryTimelineDays'],
            'notes' => $payload['notes'] ?? null,
            'status' => 'SUBMITTED'
        ]);
        $bidId = (int)$this->db->lastInsertId();
        if ($bidId === 0) {
            $stmt = $this->db->prepare('SELECT id FROM ' . Bid::TABLE . ' WHERE rfq_id = :rfq_id AND vendor_id = :vendor_id');
            $stmt->execute(['rfq_id' => $rfqId, 'vendor_id' => $vendorId]);
            $bidId = (int)($stmt->fetch()['id'] ?? 0);
        }
        return $this->find($bidId) ?? [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Bid::TABLE . ' WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function listForVendor(int $vendorId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Bid::TABLE . ' WHERE vendor_id = :vendor_id ORDER BY created_at DESC');
        $stmt->execute(['vendor_id' => $vendorId]);
        return $stmt->fetchAll();
    }

    public function accept(int $bidId, int $customerId): array
    {
        $bid = $this->find($bidId);
        if (!$bid) {
            throw new \InvalidArgumentException('Bid not found');
        }
        $rfqStmt = $this->db->prepare('SELECT * FROM ' . Rfq::TABLE . ' WHERE id = :id AND customer_id = :customer_id');
        $rfqStmt->execute(['id' => $bid['rfq_id'], 'customer_id' => $customerId]);
        $rfq = $rfqStmt->fetch();
        if (!$rfq) {
            throw new \InvalidArgumentException('RFQ not found for customer');
        }
        $this->db->prepare('UPDATE ' . Bid::TABLE . ' SET status = "ACCEPTED" WHERE id = :id')->execute(['id' => $bidId]);
        $this->db->prepare('UPDATE ' . Rfq::TABLE . ' SET status = "CLOSED" WHERE id = :id')->execute(['id' => $bid['rfq_id']]);
        return $this->orderService->createFromBid($bid, $rfq);
    }
}
