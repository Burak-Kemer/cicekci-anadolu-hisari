<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

$errors = [];
$altText = '';
$caption = '';
$sortOrder = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.';
    }

    $altText = trim((string) ($_POST['alt_text'] ?? ''));
    $caption = trim((string) ($_POST['caption'] ?? ''));
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    if (empty($_FILES['image']['name'])) {
        $errors[] = 'Bir görsel seçmelisiniz.';
    }

    if (empty($errors)) {
        $uploader = new ImageUploader(__DIR__ . '/../../uploads');

        try {
            $result = $uploader->upload($_FILES['image'], 'gallery');
            $galleryRepository = new GalleryRepository($db);
            $galleryRepository->create(
                $result['path'],
                $altText !== '' ? $altText : null,
                $caption !== '' ? $caption : null,
                $sortOrder
            );

            Flash::success('Galeri görseli eklendi.');
            header('Location: /admin/galeri/liste.php');
            exit;
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }
}

$pageTitle = 'Yeni Galeri Görseli';
$activeMenu = 'galeri';

require __DIR__ . '/../includes/admin-header.php';
?>
<form method="post" action="/admin/galeri/ekle.php" enctype="multipart/form-data" class="admin-form">
    <?php echo Csrf::field(); ?>

    <?php foreach ($errors as $error): ?>
        <div class="admin-alert admin-alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>

    <label class="admin-field">
        <span>Görsel</span>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
    </label>

    <label class="admin-field">
        <span>Alt Metin (SEO/erişilebilirlik)</span>
        <input type="text" name="alt_text" value="<?php echo htmlspecialchars($altText); ?>">
    </label>

    <label class="admin-field">
        <span>Açıklama (opsiyonel)</span>
        <input type="text" name="caption" value="<?php echo htmlspecialchars($caption); ?>">
    </label>

    <label class="admin-field">
        <span>Sıralama Değeri</span>
        <input type="number" name="sort_order" value="<?php echo $sortOrder; ?>" min="0">
    </label>

    <div class="admin-form__actions">
        <button type="submit" class="admin-btn admin-btn--primary">Kaydet</button>
        <a href="/admin/galeri/liste.php" class="admin-btn admin-btn--ghost">Vazgeç</a>
    </div>
</form>
<?php
require __DIR__ . '/../includes/admin-footer.php';
