<?php
namespace Rultivate\Services;

use Rultivate\Utils\JWT;
use Rultivate\Utils\Validator;
use Rultivate\Utils\Sanitizer;
use Rultivate\Models\Customer;
use Rultivate\Models\Vendor;
use Rultivate\Models\VendorSubscription;
use Rultivate\Utils\Database;
use PDO;

class AuthService extends BaseService
{
    private UserService $userService;
    private JWT $jwt;

    public function __construct(Database $database, UserService $userService, JWT $jwt)
    {
        parent::__construct($database);
        $this->userService = $userService;
        $this->jwt = $jwt;
    }

    public function registerCustomer(array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $missing = Validator::required($clean, ['fullName', 'email', 'phone', 'password', 'company']);
        if ($missing) {
            throw new \InvalidArgumentException('Missing fields: ' . implode(', ', $missing));
        }
        if ($this->userService->findByEmail($clean['email'])) {
            throw new \InvalidArgumentException('Email already registered');
        }
        $user = $this->userService->create([
            'email' => $clean['email'],
            'password_hash' => password_hash($clean['password'], PASSWORD_DEFAULT),
            'full_name' => $clean['fullName']
        ], ['customer']);

        $stmt = $this->db->prepare('INSERT INTO ' . Customer::TABLE . ' (user_id, company_name, phone, created_at, updated_at) VALUES (:user_id, :company_name, :phone, NOW(), NOW())');
        $stmt->execute([
            'user_id' => $user['id'],
            'company_name' => $clean['company'],
            'phone' => $clean['phone']
        ]);

        return $this->issueToken($user);
    }

    public function registerVendor(array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $missing = Validator::required($clean, ['companyName', 'fullName', 'email', 'phone', 'password']);
        if ($missing) {
            throw new \InvalidArgumentException('Missing fields: ' . implode(', ', $missing));
        }
        if ($this->userService->findByEmail($clean['email'])) {
            throw new \InvalidArgumentException('Email already registered');
        }
        $user = $this->userService->create([
            'email' => $clean['email'],
            'password_hash' => password_hash($clean['password'], PASSWORD_DEFAULT),
            'full_name' => $clean['fullName']
        ], ['vendor']);

        $stmt = $this->db->prepare('INSERT INTO ' . Vendor::TABLE . ' (user_id, company_name, slug, phone, gst_number, pan_number, status, created_at, updated_at) VALUES (:user_id, :company_name, :slug, :phone, :gst, :pan, :status, NOW(), NOW())');
        $stmt->execute([
            'user_id' => $user['id'],
            'company_name' => $clean['companyName'],
            'slug' => $this->slugify($clean['companyName']),
            'phone' => $clean['phone'],
            'gst' => $clean['gstNumber'] ?? null,
            'pan' => $clean['panNumber'] ?? null,
            'status' => 'PENDING_APPROVAL'
        ]);

        $this->db->prepare('INSERT INTO ' . VendorSubscription::TABLE . ' (vendor_id, status, created_at, updated_at) VALUES (:vendor_id, :status, NOW(), NOW())')->execute([
            'vendor_id' => $this->db->lastInsertId(),
            'status' => 'INACTIVE'
        ]);

        return $this->issueToken($user);
    }

    public function login(string $email, string $password, string $role): array
    {
        $user = $this->userService->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new \InvalidArgumentException('Invalid credentials');
        }
        if (!in_array($role, $user['roles'], true)) {
            throw new \InvalidArgumentException('Role not assigned to user');
        }
        return $this->issueToken($user);
    }

    public function issueToken(array $user): array
    {
        $primaryRole = $user['roles'][0] ?? 'guest';
        $token = $this->jwt->create([
            'sub' => $user['id'],
            'roles' => $user['roles']
        ]);
        return [
            'token' => $token,
            'user' => [
                'id' => (int)$user['id'],
                'email' => $user['email'],
                'fullName' => $user['full_name'],
                'roles' => $user['roles'],
                'primaryRole' => $primaryRole
            ]
        ];
    }

    public function me(int $userId): array
    {
        $user = $this->userService->findById($userId);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }
        return [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'fullName' => $user['full_name'],
            'roles' => $user['roles'],
            'primaryRole' => $user['roles'][0] ?? 'guest'
        ];
    }

    public function forgotPassword(string $email): array
    {
        $user = $this->userService->findByEmail($email);
        if (!$user) {
            return ['message' => 'If the email exists, reset instructions have been sent.'];
        }
        $token = bin2hex(random_bytes(16));
        $this->db->prepare('UPDATE ' . User::TABLE . ' SET reset_token = :token, reset_requested_at = NOW() WHERE id = :id')->execute([
            'token' => $token,
            'id' => $user['id']
        ]);
        return ['message' => 'Reset instructions generated', 'resetToken' => $token];
    }

    public function resetPassword(string $token, string $password): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . User::TABLE . ' WHERE reset_token = :token LIMIT 1');
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();
        if (!$user) {
            throw new \InvalidArgumentException('Invalid token');
        }
        $this->db->prepare('UPDATE ' . User::TABLE . ' SET password_hash = :password, reset_token = NULL, reset_requested_at = NULL, updated_at = NOW() WHERE id = :id')->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $user['id']
        ]);
        return ['message' => 'Password updated successfully'];
    }

    private function slugify(string $value): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $value));
        return trim($slug, '-');
    }
}
