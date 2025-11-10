<?php
namespace Rultivate\Services;

use Rultivate\Models\Customer;
use Rultivate\Models\User;
use Rultivate\Utils\Sanitizer;

class CustomerService extends BaseService
{
    public function getProfile(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT c.*, u.email, u.full_name FROM ' . Customer::TABLE . ' c INNER JOIN ' . User::TABLE . ' u ON u.id = c.user_id WHERE c.user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function updateProfile(int $userId, array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $stmt = $this->db->prepare('UPDATE ' . Customer::TABLE . ' SET company_name = :company_name, phone = :phone, address = :address, city = :city, state = :state, pin_code = :pin_code, updated_at = NOW() WHERE user_id = :user_id');
        $stmt->execute([
            'company_name' => $clean['company'] ?? null,
            'phone' => $clean['phone'] ?? null,
            'address' => $clean['address'] ?? null,
            'city' => $clean['city'] ?? null,
            'state' => $clean['state'] ?? null,
            'pin_code' => $clean['pinCode'] ?? null,
            'user_id' => $userId
        ]);
        if (isset($clean['fullName'])) {
            $this->db->prepare('UPDATE ' . User::TABLE . ' SET full_name = :full_name WHERE id = :id')->execute([
                'full_name' => $clean['fullName'],
                'id' => $userId
            ]);
        }
        $profile = $this->getProfile($userId);
        return $profile ?? [];
    }
}
