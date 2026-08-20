<?php

declare(strict_types=1);

class ProductRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function all(?int $categoryId = null): array
    {
        $sql = 'SELECT p.*, c.name AS category_name
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id';
        $params = [];

        if ($categoryId !== null) {
            $sql .= ' WHERE p.category_id = ?';
            $params[] = $categoryId;
        }

        $sql .= ' ORDER BY p.sort_order ASC, p.name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products
                (name, slug, description, price, category_id, main_image, is_featured, is_active, sort_order, meta_title, meta_description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'], $data['slug'], $data['description'], $data['price'], $data['category_id'],
            $data['main_image'], $data['is_featured'], $data['is_active'], $data['sort_order'],
            $data['meta_title'], $data['meta_description'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE products SET
                name = ?, slug = ?, description = ?, price = ?, category_id = ?,
                is_featured = ?, is_active = ?, sort_order = ?, meta_title = ?, meta_description = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $data['name'], $data['slug'], $data['description'], $data['price'], $data['category_id'],
            $data['is_featured'], $data['is_active'], $data['sort_order'], $data['meta_title'], $data['meta_description'],
            $id,
        ]);
    }

    public function updateMainImage(int $id, ?string $mainImage): void
    {
        $stmt = $this->db->prepare('UPDATE products SET main_image = ? WHERE id = ?');
        $stmt->execute([$mainImage, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function addImage(int $productId, string $path, ?string $altText, int $sortOrder): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO product_images (product_id, image_path, alt_text, sort_order) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$productId, $path, $altText, $sortOrder]);

        return (int) $this->db->lastInsertId();
    }

    public function images(int $productId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC');
        $stmt->execute([$productId]);

        return $stmt->fetchAll();
    }

    public function findImage(int $imageId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM product_images WHERE id = ?');
        $stmt->execute([$imageId]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function deleteImage(int $imageId): void
    {
        $stmt = $this->db->prepare('DELETE FROM product_images WHERE id = ?');
        $stmt->execute([$imageId]);
    }

    public function counts(): array
    {
        return [
            'total' => (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'active' => (int) $this->db->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn(),
            'featured' => (int) $this->db->query('SELECT COUNT(*) FROM products WHERE is_featured = 1')->fetchColumn(),
        ];
    }

    public function latest(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p INNER JOIN categories c ON c.id = p.category_id
             ORDER BY p.created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Public frontend: sadece aktif ürünler, opsiyonel kategori filtresiyle.
     */
    public function activeAll(?int $categoryId = null): array
    {
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                WHERE p.is_active = 1 AND c.is_active = 1';
        $params = [];

        if ($categoryId !== null) {
            $sql .= ' AND p.category_id = ?';
            $params[] = $categoryId;
        }

        $sql .= ' ORDER BY p.sort_order ASC, p.name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Public frontend: slug ile aktif ürün arar. Ürünün kendisi VEYA bağlı
     * kategorisi pasifse asla döndürmez (kategori pasife alındığında ürün
     * detay URL'si de kapanmalı).
     */
    public function findActiveBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.is_active = 1 AND c.is_active = 1'
        );
        $stmt->execute([$slug]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    /**
     * Ana sayfa "Öne Çıkan Çiçekler" için aktif + öne çıkan ürünler
     * (kategorisi de aktif olmalı).
     */
    public function activeFeatured(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p INNER JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1 AND p.is_featured = 1 AND c.is_active = 1
             ORDER BY p.sort_order ASC, p.name ASC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Ürün detay sayfasındaki "Benzer Çiçekler" için aynı kategoriden aktif
     * ürünler (kategorisi de aktif olmalı — bu metod normalde yalnızca zaten
     * aktif-kategorili bir üründen çağrılır, ama savunmacı olarak burada da
     * kontrol edilir).
     */
    public function similar(int $excludeProductId, int $categoryId, int $limit = 4): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p INNER JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1 AND c.is_active = 1 AND p.category_id = ? AND p.id != ?
             ORDER BY p.sort_order ASC, p.name ASC LIMIT ?'
        );
        $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(2, $excludeProductId, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
