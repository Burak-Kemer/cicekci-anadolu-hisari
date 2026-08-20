<?php

declare(strict_types=1);

class CategoryRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM categories ORDER BY sort_order ASC, name ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, slug, sort_order, is_active) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$data['name'], $data['slug'], $data['sort_order'], $data['is_active']]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE categories SET name = ?, slug = ?, sort_order = ?, is_active = ? WHERE id = ?'
        );
        $stmt->execute([$data['name'], $data['slug'], $data['sort_order'], $data['is_active'], $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function countProducts(int $id): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    }

    /**
     * Public frontend: sadece aktif kategoriler, sıralı.
     */
    public function activeAll(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Public frontend: slug ile aktif kategori arar, pasif/olmayan kategoriyi asla döndürmez.
     */
    public function findActiveBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE slug = ? AND is_active = 1');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }
}
