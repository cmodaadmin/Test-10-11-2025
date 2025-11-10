<?php
namespace Rultivate\Services;

use Rultivate\Models\Order;
use Rultivate\Models\OrderItem;
use Rultivate\Models\Payment;
use Rultivate\Utils\Database;

class OrderService extends BaseService
{
    public function __construct(Database $database)
    {
        parent::__construct($database);
    }

    public function createFromBid(array $bid, array $rfq): array
    {
        $stmt = $this->db->prepare('INSERT INTO ' . Order::TABLE . ' (rfq_id, customer_id, vendor_id, bid_id, status, total_amount_inr, created_at, updated_at) VALUES (:rfq_id, :customer_id, :vendor_id, :bid_id, :status, :total, NOW(), NOW())');
        $stmt->execute([
            'rfq_id' => $bid['rfq_id'],
            'customer_id' => $rfq['customer_id'],
            'vendor_id' => $bid['vendor_id'],
            'bid_id' => $bid['id'],
            'status' => 'ACCEPTED',
            'total' => $bid['amount_inr']
        ]);
        $orderId = (int)$this->db->lastInsertId();
        return $this->find($orderId) ?? [];
    }

    public function listForCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Order::TABLE . ' WHERE customer_id = :customer_id ORDER BY created_at DESC');
        $stmt->execute(['customer_id' => $customerId]);
        return $stmt->fetchAll();
    }

    public function listForVendor(int $vendorId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Order::TABLE . ' WHERE vendor_id = :vendor_id ORDER BY created_at DESC');
        $stmt->execute(['vendor_id' => $vendorId]);
        return $stmt->fetchAll();
    }

    public function find(int $orderId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Order::TABLE . ' WHERE id = :id');
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch();
        if ($order) {
            $order['items'] = $this->listItems($orderId);
        }
        return $order ?: null;
    }

    public function listItems(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . OrderItem::TABLE . ' WHERE order_id = :order_id');
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function listPayments(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Payment::TABLE . ' WHERE order_id = :order_id');
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }
}
