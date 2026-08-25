<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

$categoryRepository = new CategoryRepository($db);
$categories = $categoryRepository->all();

$pageTitle = 'Kategoriler';
$activeMenu = 'kategoriler';

require __DIR__ . '/../includes/admin-header.php';
?>
<div class="admin-toolbar">
    <a href="/admin/kategoriler/ekle.php" class="admin-btn admin-btn--primary">+ Yeni Kategori</a>
</div>

<?php if (empty($categories)): ?>
    <p class="admin-empty">Henüz kategori eklenmemiş.</p>
<?php else: ?>
    <div class="admin-table-wrap">
    <table class="admin-table admin-table--categories">
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Ad</th>
                <th>Slug</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo (int) $category['sort_order']; ?></td>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td><code><?php echo htmlspecialchars($category['slug']); ?></code></td>
                    <td>
                        <span class="admin-badge admin-badge--<?php echo $category['is_active'] ? 'success' : 'muted'; ?>">
                            <?php echo $category['is_active'] ? 'Aktif' : 'Pasif'; ?>
                        </span>
                    </td>
                    <td class="admin-table__actions">
                        <a href="/admin/kategoriler/duzenle.php?id=<?php echo (int) $category['id']; ?>" class="admin-link">Düzenle</a>
                        <form method="post" action="/admin/kategoriler/sil.php" class="admin-inline-form" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                            <?php echo Csrf::field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int) $category['id']; ?>">
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
