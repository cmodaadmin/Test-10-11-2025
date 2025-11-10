<?php
namespace Rultivate\Services;

use Rultivate\Models\CmsPage;
use Rultivate\Utils\Sanitizer;

class CmsService extends BaseService
{
    public function list(): array
    {
        $stmt = $this->db->query('SELECT * FROM ' . CmsPage::TABLE . ' ORDER BY updated_at DESC');
        return $stmt->fetchAll();
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . CmsPage::TABLE . ' WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function update(string $slug, array $payload): array
    {
        $clean = Sanitizer::cleanArray($payload);
        $stmt = $this->db->prepare('UPDATE ' . CmsPage::TABLE . ' SET title = :title, content = :content, status = :status, updated_at = NOW() WHERE slug = :slug');
        $stmt->execute([
            'title' => $clean['title'] ?? null,
            'content' => $clean['content'] ?? null,
            'status' => $clean['status'] ?? 'PUBLISHED',
            'slug' => $slug
        ]);
        return $this->getBySlug($slug) ?? [];
    }
}
