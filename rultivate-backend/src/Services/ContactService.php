<?php
namespace Rultivate\Services;

use Rultivate\Models\ContactSubmission;
use Rultivate\Utils\Sanitizer;

class ContactService extends BaseService
{
    public function submit(array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $stmt = $this->db->prepare('INSERT INTO ' . ContactSubmission::TABLE . ' (name, email, phone, company, message, created_at) VALUES (:name, :email, :phone, :company, :message, NOW())');
        $stmt->execute([
            'name' => $clean['name'],
            'email' => $clean['email'],
            'phone' => $clean['phone'],
            'company' => $clean['company'] ?? null,
            'message' => $clean['message']
        ]);
        return ['message' => 'Thanks for reaching out. Our support team will contact you shortly.'];
    }

    public function list(): array
    {
        $stmt = $this->db->query('SELECT * FROM ' . ContactSubmission::TABLE . ' ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }
}
