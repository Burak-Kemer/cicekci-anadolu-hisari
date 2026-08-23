<?php

declare(strict_types=1);

class GalleryController
{
    public static function index(PDO $db, array $settings): void
    {
        $galleryRepository = new GalleryRepository($db);
        $images = $galleryRepository->all();

        $siteName = SettingsRepository::siteName($settings);
        $pageTitle = 'Galeri | ' . $siteName . ' Beykoz Çiçekçi';

        $rawDescription = $siteName . ' stüdyosundan gerçek tasarımlar ve aranjmanlar.';
        $metaDescription = Seo::truncateDescription($rawDescription, 160);

        $canonicalPath = '/galeri';
        $ogType = 'website';
        $ogImage = !empty($images[0]['image_path']) ? Seo::absoluteUrl((string) $images[0]['image_path']) : null;

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/gallery/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
