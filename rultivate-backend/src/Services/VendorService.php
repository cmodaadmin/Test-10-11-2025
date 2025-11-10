<?php
namespace Rultivate\Services;

use Rultivate\Models\Vendor;
use Rultivate\Models\Service as VendorServiceModel;
use Rultivate\Models\VendorSubscription;
use Rultivate\Models\User;
use Rultivate\Utils\Sanitizer;

class VendorService extends BaseService
{
    public function listPublic(array $filters = []): array
    {
        $sql = 'SELECT v.*, vs.status as subscription_status FROM ' . Vendor::TABLE . ' v LEFT JOIN ' . VendorSubscription::TABLE . ' vs ON vs.vendor_id = v.id WHERE 1=1';
        $params = [];
        if (empty($filters['include_all'])) {
            $sql .= ' AND v.status = :status';
            $params['status'] = $filters['status'] ?? 'APPROVED';
        } elseif (!empty($filters['status'])) {
            $sql .= ' AND v.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND v.company_name LIKE :search';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $vendors = $stmt->fetchAll();
        foreach ($vendors as &$vendor) {
            $vendor['services'] = $this->listServices((int)$vendor['id']);
        }
        return $vendors;
    }

    public function getPublic(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT v.*, vs.status as subscription_status FROM ' . Vendor::TABLE . ' v LEFT JOIN ' . VendorSubscription::TABLE . ' vs ON vs.vendor_id = v.id WHERE v.slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $vendor = $stmt->fetch();
        if (!$vendor) {
            return null;
        }
        $vendor['services'] = $this->listServices((int)$vendor['id']);
        return $vendor;
    }

    public function getVendorIdByUser(int $userId): ?int
    {
        $stmt = $this->db->prepare('SELECT id FROM ' . Vendor::TABLE . ' WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $vendor = $stmt->fetch();
        return $vendor ? (int)$vendor['id'] : null;
    }

    public function getProfile(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT v.*, u.email, u.full_name FROM ' . Vendor::TABLE . ' v INNER JOIN ' . User::TABLE . ' u ON u.id = v.user_id WHERE v.user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $vendor = $stmt->fetch();
        if ($vendor) {
            $vendor['services'] = $this->listServices((int)$vendor['id']);
        }
        return $vendor ?: null;
    }

    public function updateProfile(int $userOrVendorId, array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $vendor = $this->getProfile($userOrVendorId);
        if (!$vendor) {
            $stmt = $this->db->prepare('SELECT * FROM ' . Vendor::TABLE . ' WHERE id = :id');
            $stmt->execute(['id' => $userOrVendorId]);
            $vendor = $stmt->fetch();
            if (!$vendor) {
                throw new \InvalidArgumentException('Vendor not found');
            }
            $userId = (int)$vendor['user_id'];
        } else {
            $userId = (int)$vendor['user_id'];
        }
        $stmt = $this->db->prepare('UPDATE ' . Vendor::TABLE . ' SET company_name = :company_name, gst_number = :gst, pan_number = :pan, city = :city, state = :state, address = :address, status = COALESCE(:status, status), updated_at = NOW() WHERE user_id = :user_id');
        $stmt->execute([
            'company_name' => $clean['companyName'] ?? $vendor['company_name'] ?? null,
            'gst' => $clean['gstNumber'] ?? $vendor['gst_number'] ?? null,
            'pan' => $clean['panNumber'] ?? $vendor['pan_number'] ?? null,
            'city' => $clean['city'] ?? $vendor['city'] ?? null,
            'state' => $clean['state'] ?? $vendor['state'] ?? null,
            'address' => $clean['address'] ?? $vendor['address'] ?? null,
            'status' => $clean['status'] ?? $vendor['status'] ?? null,
            'user_id' => $userId
        ]);
        if (isset($clean['fullName'])) {
            $this->db->prepare('UPDATE ' . User::TABLE . ' SET full_name = :full_name WHERE id = :id')->execute([
                'full_name' => $clean['fullName'],
                'id' => $userId
            ]);
        }
        return $this->getProfile($userId) ?? [];
    }

    public function listServices(int $vendorId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . VendorServiceModel::TABLE . ' WHERE vendor_id = :vendor_id');
        $stmt->execute(['vendor_id' => $vendorId]);
        return $stmt->fetchAll();
    }
}
