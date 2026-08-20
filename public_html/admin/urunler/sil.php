<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
    Flash::error('Geçersiz istek.');
    header('Location: /admin/urunler/liste.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$productRepository = new ProductRepository($db);
$product = $productRepository->find($id);

if ($product === null) {
    Flash::error('Ürün bulunamadı.');
    header('Location: /admin/urunler/liste.php');
    exit;
}

$images = $productRepository->images($id);
$uploader = new ImageUploader(__DIR__ . '/../../uploads');

$productRepository->delete($id);

if (!empty($product['main_image'])) {
    $uploader->delete($product['main_image']);
}

foreach ($images as $image) {
    $uploader->delete($image['image_path']);
}

Flash::success('Ürün silindi.');
header('Location: /admin/urunler/liste.php');
exit;
