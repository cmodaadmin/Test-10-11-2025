<?php
namespace Rultivate\Services;

use Rultivate\Models\SubscriptionPlan;
use Rultivate\Models\VendorSubscription;
use Rultivate\Models\Payment;
use Rultivate\Utils\Sanitizer;

class SubscriptionService extends BaseService
{
    public function listPlans(): array
    {
        $stmt = $this->db->query('SELECT * FROM ' . SubscriptionPlan::TABLE . ' WHERE is_active = 1 ORDER BY price_inr ASC');
        return $stmt->fetchAll();
    }

    public function createPlan(array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $stmt = $this->db->prepare('INSERT INTO ' . SubscriptionPlan::TABLE . ' (name, description, price_inr, billing_cycle, max_rfqs_per_month, max_team_members, is_active, created_at, updated_at) VALUES (:name, :description, :price_inr, :billing_cycle, :max_rfqs_per_month, :max_team_members, :is_active, NOW(), NOW())');
        $stmt->execute([
            'name' => $clean['name'],
            'description' => $clean['description'] ?? null,
            'price_inr' => $clean['priceInr'],
            'billing_cycle' => $clean['billingCycle'],
            'max_rfqs_per_month' => $clean['maxRfqsPerMonth'] ?? null,
            'max_team_members' => $clean['maxTeamMembers'] ?? null,
            'is_active' => $clean['isActive'] ?? 1
        ]);
        return $this->findPlan((int)$this->db->lastInsertId()) ?? [];
    }

    public function updatePlan(int $id, array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $stmt = $this->db->prepare('UPDATE ' . SubscriptionPlan::TABLE . ' SET name = :name, description = :description, price_inr = :price_inr, billing_cycle = :billing_cycle, max_rfqs_per_month = :max_rfqs_per_month, max_team_members = :max_team_members, is_active = :is_active, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'name' => $clean['name'],
            'description' => $clean['description'] ?? null,
            'price_inr' => $clean['priceInr'],
            'billing_cycle' => $clean['billingCycle'],
            'max_rfqs_per_month' => $clean['maxRfqsPerMonth'] ?? null,
            'max_team_members' => $clean['maxTeamMembers'] ?? null,
            'is_active' => $clean['isActive'] ?? 1,
            'id' => $id
        ]);
        return $this->findPlan($id) ?? [];
    }

    public function deletePlan(int $id): void
    {
        $this->db->prepare('DELETE FROM ' . SubscriptionPlan::TABLE . ' WHERE id = :id')->execute(['id' => $id]);
    }

    public function findPlan(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . SubscriptionPlan::TABLE . ' WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getVendorSubscription(int $vendorId): ?array
    {
        $stmt = $this->db->prepare('SELECT vs.*, sp.name as plan_name FROM ' . VendorSubscription::TABLE . ' vs LEFT JOIN ' . SubscriptionPlan::TABLE . ' sp ON sp.id = vs.plan_id WHERE vs.vendor_id = :vendor_id');
        $stmt->execute(['vendor_id' => $vendorId]);
        return $stmt->fetch() ?: null;
    }

    public function activatePlan(int $vendorId, int $planId): array
    {
        $plan = $this->findPlan($planId);
        if (!$plan) {
            throw new \InvalidArgumentException('Plan not found');
        }
        $stmt = $this->db->prepare('INSERT INTO ' . VendorSubscription::TABLE . ' (vendor_id, plan_id, status, activated_at, expires_at, created_at, updated_at) VALUES (:vendor_id, :plan_id, :status, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), NOW(), NOW()) ON DUPLICATE KEY UPDATE plan_id = VALUES(plan_id), status = "ACTIVE", activated_at = NOW(), expires_at = DATE_ADD(NOW(), INTERVAL 30 DAY), updated_at = NOW()');
        $stmt->execute([
            'vendor_id' => $vendorId,
            'plan_id' => $planId,
            'status' => 'ACTIVE'
        ]);
        $this->db->prepare('INSERT INTO ' . Payment::TABLE . ' (vendor_id, amount_inr, status, method, reference, created_at, updated_at) VALUES (:vendor_id, :amount_inr, :status, :method, :reference, NOW(), NOW())')->execute([
            'vendor_id' => $vendorId,
            'amount_inr' => $plan['price_inr'],
            'status' => 'SUCCESS',
            'method' => 'RAZORPAY-MOCK',
            'reference' => 'SUB-' . uniqid()
        ]);
        return $this->getVendorSubscription($vendorId) ?? [];
    }
}
