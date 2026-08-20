<?php

declare(strict_types=1);

class SitemapController
{
    public static function robotsTxt(): void
    {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "User-agent: *\n";
        echo "Disallow: /admin/\n";
        echo "Allow: /\n";
        echo "\n";
        echo 'Sitemap: ' . Seo::canonicalUrl('/sitemap.xml') . "\n";
    }

    public static function sitemap(PDO $db): void
    {
        $categoryRepository = new CategoryRepository($db);
        $productRepository = new ProductRepository($db);

        $urls = [];
        $urls[] = ['loc' => Seo::canonicalUrl('/'), 'changefreq' => 'weekly', 'priority' => '1.0'];
        $urls[] = ['loc' => Seo::canonicalUrl('/cicekler'), 'changefreq' => 'daily', 'priority' => '0.9'];
        $urls[] = ['loc' => Seo::canonicalUrl('/galeri'), 'changefreq' => 'weekly', 'priority' => '0.5'];
        $urls[] = ['loc' => Seo::canonicalUrl('/hakkimizda'), 'changefreq' => 'monthly', 'priority' => '0.4'];
        $urls[] = ['loc' => Seo::canonicalUrl('/iletisim'), 'changefreq' => 'monthly', 'priority' => '0.4'];

        // Yalnızca aktif kategoriler (activeAll zaten is_active = 1 filtreler).
        foreach ($categoryRepository->activeAll() as $category) {
            $urls[] = [
                'loc' => Seo::canonicalUrl('/cicekler/' . $category['slug']),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // Yalnızca aktif VE kategorisi aktif olan ürünler (activeAll şimdi
        // c.is_active = 1 şartını da içeriyor).
        foreach ($productRepository->activeAll() as $product) {
            $urls[] = [
                'loc' => Seo::canonicalUrl('/urun/' . $product['slug']),
                'changefreq' => 'weekly',
                'priority' => '0.6',
                'lastmod' => self::lastmod($product['updated_at'] ?? null),
            ];
        }

        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            echo '  <url>' . "\n";
            echo '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>' . "\n";
            if (!empty($url['lastmod'])) {
                echo '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</lastmod>' . "\n";
            }
            echo '    <changefreq>' . htmlspecialchars($url['changefreq'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</changefreq>' . "\n";
            echo '    <priority>' . htmlspecialchars($url['priority'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</priority>' . "\n";
            echo '  </url>' . "\n";
        }

        echo '</urlset>' . "\n";
    }

    private static function lastmod(?string $datetime): ?string
    {
        if ($datetime === null || $datetime === '') {
            return null;
        }

        $timestamp = strtotime($datetime);

        return $timestamp !== false ? date('Y-m-d', $timestamp) : null;
    }
}
