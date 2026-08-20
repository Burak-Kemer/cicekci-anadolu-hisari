<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
    Flash::error('Geçersiz istek.');
    header('Location: /admin/galeri/liste.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$galleryRepository = new GalleryRepository($db);
$image = $galleryRepository->find($id);

if ($image === null) {
    Flash::error('Görsel bulunamadı.');
    header('Location: /admin/galeri/liste.php');
    exit;
}

$uploader = new ImageUploader(__DIR__ . '/../../uploads');
$uploader->delete($image['image_path']);
$galleryRepository->delete($id);

Flash::success('Görsel silindi.');
header('Location: /admin/galeri/liste.php');
exit;
