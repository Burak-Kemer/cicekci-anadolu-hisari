<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

$galleryRepository = new GalleryRepository($db);
$images = $galleryRepository->all();

$pageTitle = 'Galeri';
$activeMenu = 'galeri';

require __DIR__ . '/../includes/admin-header.php';
?>
<div class="admin-toolbar">
    <a href="/admin/galeri/ekle.php" class="admin-btn admin-btn--primary">+ Yeni Görsel Ekle</a>
</div>

<?php if (empty($images)): ?>
    <p class="admin-empty">Henüz galeri görseli eklenmemiş.</p>
<?php else: ?>
    <div class="admin-image-grid">
        <?php foreach ($images as $image): ?>
            <div class="admin-image-grid__item">
                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars((string) $image['alt_text']); ?>">
                <p class="admin-image-grid__caption"><?php echo htmlspecialchars((string) $image['caption']); ?></p>
                <div class="admin-image-grid__actions">
                    <a href="/admin/galeri/duzenle.php?id=<?php echo (int) $image['id']; ?>" class="admin-link">Düzenle</a>
                    <form method="post" action="/admin/galeri/sil.php" class="admin-inline-form" onsubmit="return confirm('Bu görseli silmek istediğinize emin misiniz?');">
                        <?php echo Csrf::field(); ?>
                        <input type="hidden" name="id" value="<?php echo (int) $image['id']; ?>">
                        <button type="submit" class="admin-link admin-link--danger">Sil</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
require __DIR__ . '/../includes/admin-footer.php';
