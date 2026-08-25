<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

$categoryRepository = new CategoryRepository($db);
$productRepository = new ProductRepository($db);

$categoryFilter = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int) $_GET['category_id'] : null;
$products = $productRepository->all($categoryFilter);
$categories = $categoryRepository->all();

$pageTitle = 'Ürünler';
$activeMenu = 'urunler';

require __DIR__ . '/../includes/admin-header.php';
?>
<div class="admin-toolbar">
    <a href="/admin/urunler/ekle.php" class="admin-btn admin-btn--primary">+ Yeni Ürün</a>

    <form method="get" action="/admin/urunler/liste.php" class="admin-inline-form">
        <select name="category_id" class="admin-select" onchange="this.form.submit()">
            <option value="">Tüm Kategoriler</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo (int) $category['id']; ?>" <?php echo $categoryFilter === (int) $category['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if (empty($products)): ?>
    <p class="admin-empty">Kayıtlı ürün bulunamadı.</p>
<?php else: ?>
    <div class="admin-table-wrap">
    <table class="admin-table admin-table--products">
        <thead>
            <tr>
                <th>Görsel</th>
                <th>Ürün Adı</th>
                <th>Kategori</th>
                <th>Fiyat</th>
                <th>Durum</th>
                <th>Öne Çıkan</th>
                <th>Sıra</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <?php if (!empty($product['main_image'])): ?>
                            <img src="<?php echo htmlspecialchars($product['main_image']); ?>" alt="" class="admin-thumb">
                        <?php else: ?>
                            <span class="admin-thumb admin-thumb--empty">Görsel yok</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td>
                        <?php if (($product['price_status'] ?? 'fixed') === 'contact' || $product['price'] === null): ?>
                            <span class="admin-badge admin-badge--muted">İletişime Geçin</span>
                        <?php else: ?>
                            <?php echo number_format((float) $product['price'], 2, ',', '.'); ?> ₺
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="admin-badge admin-badge--<?php echo $product['is_active'] ? 'success' : 'muted'; ?>">
                            <?php echo $product['is_active'] ? 'Aktif' : 'Pasif'; ?>
                        </span>
                    </td>
                    <td><?php echo $product['is_featured'] ? 'Evet' : '—'; ?></td>
                    <td><?php echo (int) $product['sort_order']; ?></td>
                    <td class="admin-table__actions">
                        <a href="/admin/urunler/duzenle.php?id=<?php echo (int) $product['id']; ?>" class="admin-link">Düzenle</a>
                        <form method="post" action="/admin/urunler/sil.php" class="admin-inline-form" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz? Bu işlem geri alınamaz.');">
                            <?php echo Csrf::field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int) $product['id']; ?>">
                            <button type="submit" class="admin-link admin-link--danger">Sil</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>
<?php
require __DIR__ . '/../includes/admin-footer.php';
