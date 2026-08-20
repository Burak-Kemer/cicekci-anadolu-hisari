<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

$categoryRepository = new CategoryRepository($db);

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$category = $id > 0 ? $categoryRepository->find($id) : null;

if ($category === null) {
    Flash::error('Kategori bulunamadı.');
    header('Location: /admin/kategoriler/liste.php');
    exit;
}

$errors = [];
$name = $category['name'];
$sortOrder = (int) $category['sort_order'];
$isActive = (bool) $category['is_active'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.';
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']);

    if ($name === '') {
        $errors[] = 'Kategori adı gereklidir.';
    }

    if (empty($errors)) {
        $baseSlug = Slug::generate($name);
        $slug = Slug::unique($db, 'categories', $baseSlug, $id);

        $categoryRepository->update($id, [
            'name' => $name,
            'slug' => $slug,
            'sort_order' => $sortOrder,
            'is_active' => $isActive ? 1 : 0,
        ]);

        Flash::success('Kategori güncellendi.');
        header('Location: /admin/kategoriler/liste.php');
        exit;
    }
}

$pageTitle = 'Kategori Düzenle';
$activeMenu = 'kategoriler';

require __DIR__ . '/../includes/admin-header.php';
?>
<form method="post" action="/admin/kategoriler/duzenle.php?id=<?php echo $id; ?>" class="admin-form">
    <?php echo Csrf::field(); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <?php foreach ($errors as $error): ?>
        <div class="admin-alert admin-alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>

    <label class="admin-field">
        <span>Kategori Adı</span>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
    </label>

    <label class="admin-field">
        <span>Sıralama Değeri</span>
        <input type="number" name="sort_order" value="<?php echo $sortOrder; ?>" min="0">
    </label>

    <label class="admin-field admin-field--checkbox">
        <input type="checkbox" name="is_active" <?php echo $isActive ? 'checked' : ''; ?>>
        <span>Aktif</span>
    </label>

    <div class="admin-form__actions">
        <button type="submit" class="admin-btn admin-btn--primary">Güncelle</button>
        <a href="/admin/kategoriler/liste.php" class="admin-btn admin-btn--ghost">Vazgeç</a>
    </div>
</form>
<?php
require __DIR__ . '/../includes/admin-footer.php';
