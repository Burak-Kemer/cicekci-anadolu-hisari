<?php

declare(strict_types=1);

class ProductController
{
    public static function catalog(PDO $db, array $settings, ?string $categorySlug): bool
    {
        $productRepository = new ProductRepository($db);
        $categoryRepository = new CategoryRepository($db);

        $categories = $categoryRepository->activeAll();
        $activeCategory = null;

        if ($categorySlug !== null) {
            $activeCategory = $categoryRepository->findActiveBySlug($categorySlug);
            if ($activeCategory === null) {
                return false;
            }
        }

        $products = $productRepository->activeAll($activeCategory['id'] ?? null);

        $siteName = SettingsRepository::siteName($settings);
        $pageTitle = ($activeCategory !== null ? $activeCategory['name'] : 'Çiçeklerimiz') . ' | ' . $siteName;

        $rawDescription = $activeCategory !== null
            ? $activeCategory['name'] . ' kategorisindeki çiçeklerimizi inceleyin. Anadolu Hisarı ve çevresine hizmet veriyoruz.'
            : 'Taze ve özenle hazırlanan çiçeklerimizin tamamını inceleyin. Anadolu Hisarı ve çevresine hizmet veriyoruz.';
        $metaDescription = Seo::truncateDescription($rawDescription, 160);

        $canonicalPath = $activeCategory !== null ? '/cicekler/' . $activeCategory['slug'] : '/cicekler';
        $ogType = 'website';
        $ogImage = !empty($products[0]['main_image']) ? Seo::absoluteUrl((string) $products[0]['main_image']) : null;

        $breadcrumbItems = [['name' => 'Ana Sayfa', 'path' => '/'], ['name' => 'Çiçeklerimiz', 'path' => '/cicekler']];
        if ($activeCategory !== null) {
            $breadcrumbItems[] = ['name' => $activeCategory['name'], 'path' => '/cicekler/' . $activeCategory['slug']];
        }
        $jsonLd = [Seo::breadcrumbSchema($breadcrumbItems)];

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/products/index.php';
        require __DIR__ . '/../views/layouts/footer.php';

        return true;
    }

    public static function show(PDO $db, array $settings, string $slug): bool
    {
        $productRepository = new ProductRepository($db);

        $product = $productRepository->findActiveBySlug($slug);

        if ($product === null) {
            return false;
        }

        $images = $productRepository->images((int) $product['id']);
        $similarProducts = $productRepository->similar((int) $product['id'], (int) $product['category_id'], 4);

        $siteName = SettingsRepository::siteName($settings);
        $pageTitle = !empty($product['meta_title'])
            ? (string) $product['meta_title']
            : $product['name'] . ' | ' . $siteName;

        $rawDescription = !empty($product['meta_description'])
            ? (string) $product['meta_description']
            : (string) $product['description'];
        $metaDescription = Seo::truncateDescription($rawDescription, 160);

        $canonicalPath = '/urun/' . $product['slug'];
        $ogType = 'product';

        $ogImagePath = null;
        if (!empty($product['main_image'])) {
            $ogImagePath = (string) $product['main_image'];
        } elseif (!empty($images[0]['image_path'])) {
            $ogImagePath = (string) $images[0]['image_path'];
        }
        $ogImage = $ogImagePath !== null ? Seo::absoluteUrl($ogImagePath) : null;

        $jsonLd = [
            Seo::productSchema($product, $images),
            Seo::breadcrumbSchema([
                ['name' => 'Ana Sayfa', 'path' => '/'],
                ['name' => 'Çiçeklerimiz', 'path' => '/cicekler'],
                ['name' => $product['category_name'], 'path' => '/cicekler/' . $product['category_slug']],
                ['name' => $product['name'], 'path' => '/urun/' . $product['slug']],
            ]),
        ];

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/products/show.php';
        require __DIR__ . '/../views/layouts/footer.php';

        return true;
    }
}
