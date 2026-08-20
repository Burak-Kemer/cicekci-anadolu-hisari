<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

$galleryRepository = new GalleryRepository($db);
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$image = $id > 0 ? $galleryRepository->find($id) : null;

if ($image === null) {
    Flash::error('Görsel bulunamadı.');
    header('Location: /admin/galeri/liste.php');
    exit;
}

$errors = [];
$altText = (string) $image['alt_text'];
$caption = (string) $image['caption'];
$sortOrder = (int) $image['sort_order'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.';
    }

    $altText = trim((string) ($_POST['alt_text'] ?? ''));
    $caption = trim((string) ($_POST['caption'] ?? ''));
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    if (empty($errors)) {
        $galleryRepository->update($id, $altText !== '' ? $altText : null, $caption !== '' ? $caption : null, $sortOrder);
        Flash::success('Galeri görseli güncellendi.');
        header('Location: /admin/galeri/liste.php');
        exit;
    }
}

$pageTitle = 'Galeri Görseli Düzenle';
$activeMenu = 'galeri';

require __DIR__ . '/../includes/admin-header.php';
?>
<form method="post" action="/admin/galeri/duzenle.php?id=<?php echo $id; ?>" class="admin-form">
    <?php echo Csrf::field(); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <?php foreach ($errors as $error): ?>
        <div class="admin-alert admin-alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>

    <div class="admin-current-image">
        <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="" class="admin-thumb admin-thumb--large">
    </div>

    <label class="admin-field">
        <span>Alt Metin</span>
        <input type="text" name="alt_text" value="<?php echo htmlspecialchars($altText); ?>">
    </label>

    <label class="admin-field">
        <span>Açıklama</span>
        <input type="text" name="caption" value="<?php echo htmlspecialchars($caption); ?>">
    </label>

    <label class="admin-field">
        <span>Sıralama Değeri</span>
        <input type="number" name="sort_order" value="<?php echo $sortOrder; ?>" min="0">
    </label>

    <div class="admin-form__actions">
        <button type="submit" class="admin-btn admin-btn--primary">Güncelle</button>
        <a href="/admin/galeri/liste.php" class="admin-btn admin-btn--ghost">Vazgeç</a>
    </div>
</form>
<?php
require __DIR__ . '/../includes/admin-footer.php';
