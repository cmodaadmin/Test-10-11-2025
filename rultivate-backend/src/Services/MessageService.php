<?php
namespace Rultivate\Services;

use Rultivate\Models\Message;
use Rultivate\Utils\Sanitizer;

class MessageService extends BaseService
{
    public function list(int $threadId, int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . Message::TABLE . ' WHERE thread_id = :thread_id ORDER BY created_at ASC');
        $stmt->execute(['thread_id' => $threadId]);
        $messages = $stmt->fetchAll();
        $this->db->prepare('UPDATE ' . Message::TABLE . ' SET read_at = NOW() WHERE thread_id = :thread_id AND receiver_id = :user_id AND read_at IS NULL')->execute([
            'thread_id' => $threadId,
            'user_id' => $userId
        ]);
        return $messages;
    }

    public function send(int $threadId, int $senderId, int $receiverId, string $body): array
    {
        $cleanBody = Sanitizer::cleanString($body);
        $stmt = $this->db->prepare('INSERT INTO ' . Message::TABLE . ' (thread_id, sender_id, receiver_id, body, created_at) VALUES (:thread_id, :sender_id, :receiver_id, :body, NOW())');
        $stmt->execute([
            'thread_id' => $threadId,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'body' => $cleanBody
        ]);
        $id = (int)$this->db->lastInsertId();
        $messageStmt = $this->db->prepare('SELECT * FROM ' . Message::TABLE . ' WHERE id = :id');
        $messageStmt->execute(['id' => $id]);
        return $messageStmt->fetch() ?: [];
    }
}
