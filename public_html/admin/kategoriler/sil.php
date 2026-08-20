<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
    Flash::error('Geçersiz istek.');
    header('Location: /admin/kategoriler/liste.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$categoryRepository = new CategoryRepository($db);
$category = $categoryRepository->find($id);

if ($category === null) {
    Flash::error('Kategori bulunamadı.');
    header('Location: /admin/kategoriler/liste.php');
    exit;
}

if ($categoryRepository->countProducts($id) > 0) {
    Flash::error('Bu kategoride ürün bulunduğu için silinemez. Lütfen önce ilgili ürünleri başka bir kategoriye taşıyın veya silin.');
    header('Location: /admin/kategoriler/liste.php');
    exit;
}

try {
    $categoryRepository->delete($id);
    Flash::success('Kategori silindi.');
} catch (PDOException $e) {
    Flash::error('Bu kategori şu anda silinemiyor. Lütfen ilişkili ürünleri kontrol edin.');
}

header('Location: /admin/kategoriler/liste.php');
exit;
