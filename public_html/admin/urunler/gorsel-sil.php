<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
    Flash::error('Geçersiz istek.');
    header('Location: /admin/urunler/liste.php');
    exit;
}

$imageId = (int) ($_POST['image_id'] ?? 0);
$productId = (int) ($_POST['product_id'] ?? 0);

$productRepository = new ProductRepository($db);
$image = $productRepository->findImage($imageId);

if ($image === null || (int) $image['product_id'] !== $productId) {
    Flash::error('Görsel bulunamadı.');
    header('Location: /admin/urunler/duzenle.php?id=' . $productId);
    exit;
}

$uploader = new ImageUploader(__DIR__ . '/../../uploads');
$uploader->delete($image['image_path']);
$productRepository->deleteImage($imageId);

Flash::success('Görsel silindi.');
header('Location: /admin/urunler/duzenle.php?id=' . $productId);
exit;
