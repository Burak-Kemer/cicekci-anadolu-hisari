<?php

declare(strict_types=1);

class HomeController
{
    public static function index(PDO $db, array $settings): void
    {
        $productRepository = new ProductRepository($db);
        $categoryRepository = new CategoryRepository($db);

        $featuredProducts = $productRepository->activeFeatured(6);
        $categories = $categoryRepository->activeAll();

        $siteName = SettingsRepository::siteName($settings);
        $pageTitle = $siteName . ' | Beykoz Göksu Çiçekçi';

        $rawDescription = 'Beykoz Göksu\'da butik çiçek stüdyosu ' . $siteName . '. Güller, orkideler ve özel tasarım aranjmanlar.';
        $metaDescription = Seo::truncateDescription($rawDescription, 160);

        $canonicalPath = '/';
        $ogType = 'website';
        $ogImage = !empty($settings['logo_path']) ? Seo::absoluteUrl((string) $settings['logo_path']) : null;

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/home/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
