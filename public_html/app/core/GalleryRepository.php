<?php

declare(strict_types=1);

class GalleryRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM gallery_images ORDER BY sort_order ASC, id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM gallery_images WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function create(string $imagePath, ?string $altText, ?string $caption, int $sortOrder): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO gallery_images (image_path, alt_text, caption, sort_order) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$imagePath, $altText, $caption, $sortOrder]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, ?string $altText, ?string $caption, int $sortOrder): void
    {
        $stmt = $this->db->prepare(
            'UPDATE gallery_images SET alt_text = ?, caption = ?, sort_order = ? WHERE id = ?'
        );
        $stmt->execute([$altText, $caption, $sortOrder, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM gallery_images WHERE id = ?');
        $stmt->execute([$id]);
    }
}
