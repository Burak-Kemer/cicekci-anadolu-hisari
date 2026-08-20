<?php

declare(strict_types=1);

class HomeController
{
    public static function index(PDO $db, array $settings): void
    {
        $productRepository = new ProductRepository($db);
        $categoryRepository = new CategoryRepository($db);
        $galleryRepository = new GalleryRepository($db);

        $featuredProducts = $productRepository->activeFeatured(6);
        $categories = $categoryRepository->activeAll();
        $galleryPreview = array_slice($galleryRepository->all(), 0, 6);

        $siteName = SettingsRepository::siteName($settings);
        $pageTitle = $siteName . ' | Anadolu Hisarı Çiçekçi';

        $rawDescription = SettingsRepository::isPlaceholder($settings['default_meta_description'] ?? null)
            ? 'Anadolu Hisarı ve çevresinde taze çiçekler, özel tasarım buketler ve çiçek aranjmanları. Kataloğumuzu inceleyin, WhatsApp veya telefonla iletişime geçin.'
            : (string) $settings['default_meta_description'];
        $metaDescription = Seo::truncateDescription($rawDescription, 160);

        $canonicalPath = '/';
        $ogType = 'website';
        $ogImage = !empty($settings['logo_path']) ? Seo::absoluteUrl((string) $settings['logo_path']) : null;

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/home/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
