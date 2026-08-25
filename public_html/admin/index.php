<?php

declare(strict_types=1);

require __DIR__ . '/includes/auth-check.php';

$categoryRepository = new CategoryRepository($db);
$productRepository = new ProductRepository($db);

$productCounts = $productRepository->counts();
$categoryCount = $categoryRepository->count();
$latestProducts = $productRepository->latest(5);

$pageTitle = 'Dashboard';
$activeMenu = 'dashboard';

require __DIR__ . '/includes/admin-header.php';
?>
<section class="admin-stat-grid">
    <div class="admin-stat-card">
        <span class="admin-stat-card__label">Toplam Ürün</span>
        <span class="admin-stat-card__value"><?php echo $productCounts['total']; ?></span>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-card__label">Aktif Ürün</span>
        <span class="admin-stat-card__value"><?php echo $productCounts['active']; ?></span>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-card__label">Toplam Kategori</span>
        <span class="admin-stat-card__value"><?php echo $categoryCount; ?></span>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-card__label">Öne Çıkan Ürün</span>
        <span class="admin-stat-card__value"><?php echo $productCounts['featured']; ?></span>
    </div>
</section>

<section class="admin-panel">
    <h2 class="admin-panel__title">Son Eklenen Ürünler</h2>
    <?php if (empty($latestProducts)): ?>
        <p class="admin-empty">Henüz ürün eklenmemiş.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ürün Adı</th>
                    <th>Kategori</th>
                    <th>Fiyat</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestProducts as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td>
                            <?php if (($product['price_status'] ?? 'fixed') === 'contact' || $product['price'] === null): ?>
                                İletişime Geçin
                            <?php else: ?>
                                <?php echo number_format((float) $product['price'], 2, ',', '.'); ?> ₺
                            <?php endif; ?>
                        </td>
                        <td><?php echo $product['is_active'] ? 'Aktif' : 'Pasif'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</section>
<?php
require __DIR__ . '/includes/admin-footer.php';
