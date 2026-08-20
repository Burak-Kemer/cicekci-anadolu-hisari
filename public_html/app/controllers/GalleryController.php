<?php

declare(strict_types=1);

class GalleryController
{
    public static function index(PDO $db, array $settings): void
    {
        $galleryRepository = new GalleryRepository($db);
        $images = $galleryRepository->all();

        $siteName = SettingsRepository::siteName($settings);
        $pageTitle = 'Galeri | ' . $siteName;

        $rawDescription = SettingsRepository::isPlaceholder($settings['default_meta_description'] ?? null)
            ? 'Hazırladığımız buket ve çiçek aranjmanlarından örnekler.'
            : (string) $settings['default_meta_description'];
        $metaDescription = Seo::truncateDescription($rawDescription, 160);

        $canonicalPath = '/galeri';
        $ogType = 'website';
        $ogImage = !empty($images[0]['image_path']) ? Seo::absoluteUrl((string) $images[0]['image_path']) : null;

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/gallery/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
