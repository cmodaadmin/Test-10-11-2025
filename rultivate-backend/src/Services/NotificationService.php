<?php
namespace Rultivate\Services;

use Rultivate\Models\Notification;
use Rultivate\Utils\Sanitizer;

class NotificationService extends BaseService
{
    public function list(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Notification::TABLE . ' WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function markAsRead(int $notificationId, int $userId): array
    {
        $this->db->prepare('UPDATE ' . Notification::TABLE . ' SET is_read = 1, read_at = NOW() WHERE id = :id AND user_id = :user_id')->execute([
            'id' => $notificationId,
            'user_id' => $userId
        ]);
        $stmt = $this->db->prepare('SELECT * FROM ' . Notification::TABLE . ' WHERE id = :id');
        $stmt->execute(['id' => $notificationId]);
        return $stmt->fetch() ?: [];
    }

    public function broadcast(array $payload, array $userIds): void
    {
        $clean = Sanitizer::cleanArray($payload);
        foreach ($userIds as $userId) {
            $this->db->prepare('INSERT INTO ' . Notification::TABLE . ' (user_id, title, content, type, is_read, created_at, updated_at) VALUES (:user_id, :title, :content, :type, 0, NOW(), NOW())')->execute([
                'user_id' => $userId,
                'title' => $clean['title'],
                'content' => $clean['content'],
                'type' => $clean['type'] ?? 'general'
            ]);
        }
    }
}
