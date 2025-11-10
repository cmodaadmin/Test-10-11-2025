<?php
namespace Rultivate\Services;

use PDO;
use Rultivate\Models\User;
use Rultivate\Models\Role;
use Rultivate\Models\UserRole;

class UserService extends BaseService
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . User::TABLE . ' WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if (!$user) {
            return null;
        }
        $user['roles'] = $this->getRoles((int)$user['id']);
        return $user;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . User::TABLE . ' WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if (!$user) {
            return null;
        }
        $user['roles'] = $this->getRoles($id);
        return $user;
    }

    public function create(array $data, array $roles): array
    {
        $stmt = $this->db->prepare('INSERT INTO ' . User::TABLE . ' (email, password_hash, full_name, status, created_at, updated_at) VALUES (:email, :password_hash, :full_name, :status, NOW(), NOW())');
        $stmt->execute([
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'full_name' => $data['full_name'] ?? null,
            'status' => $data['status'] ?? 'ACTIVE'
        ]);
        $userId = (int)$this->db->lastInsertId();
        $this->assignRoles($userId, $roles);
        return $this->findById($userId);
    }

    public function assignRoles(int $userId, array $roles): void
    {
        $this->db->prepare('DELETE FROM ' . UserRole::TABLE . ' WHERE user_id = :user_id')->execute(['user_id' => $userId]);
        foreach ($roles as $role) {
            $roleId = $this->resolveRoleId($role);
            $stmt = $this->db->prepare('INSERT INTO ' . UserRole::TABLE . ' (user_id, role_id, created_at) VALUES (:user_id, :role_id, NOW())');
            $stmt->execute([
                'user_id' => $userId,
                'role_id' => $roleId
            ]);
        }
    }

    public function getRoles(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT r.slug FROM ' . Role::TABLE . ' r INNER JOIN ' . UserRole::TABLE . ' ur ON ur.role_id = r.id WHERE ur.user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        return array_column($stmt->fetchAll(), 'slug');
    }

    private function resolveRoleId(string $slug): int
    {
        $stmt = $this->db->prepare('SELECT id FROM ' . Role::TABLE . ' WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $role = $stmt->fetch();
        if ($role) {
            return (int)$role['id'];
        }
        $insert = $this->db->prepare('INSERT INTO ' . Role::TABLE . ' (name, slug, created_at, updated_at) VALUES (:name, :slug, NOW(), NOW())');
        $insert->execute([
            'name' => ucfirst(str_replace('_', ' ', $slug)),
            'slug' => $slug
        ]);
        return (int)$this->db->lastInsertId();
    }
}
