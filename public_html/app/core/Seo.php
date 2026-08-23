<?php

declare(strict_types=1);

// Merkezi SEO/metadata yardımcı sınıfı: canonical/OG/Twitter URL üretimi,
// güvenli açıklama kısaltma ve JSON-LD şema üretimi tek noktadan yönetilir.
// Hiçbir metod placeholder veriyi gerçek işletme verisi gibi şemaya koymaz.

class Seo
{
    /**
     * Gelen isteğin gerçek host'undan üretir (asla yanlış/uydurma domain
     * döndürmez). Gerçek domain netleşip SABİT bir canonical host istenirse
     * config/config.php'deki SITE_CANONICAL_HOST doldurulmalı.
     */
    public static function baseUrl(): string
    {
        if (defined('SITE_CANONICAL_HOST') && SITE_CANONICAL_HOST !== null && SITE_CANONICAL_HOST !== '') {
            return 'https://' . SITE_CANONICAL_HOST;
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host;
    }

    /**
     * Query string dahil edilmez, trailing slash normalize edilir (kök hariç).
     */
    public static function canonicalUrl(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return self::baseUrl() . $path;
    }

    /**
     * Zaten mutlak (http/https) bir URL ise olduğu gibi, değilse baseUrl ile
     * birleştirerek döner. Görsel yollarını (uploads/...) OG/JSON-LD için
     * mutlak hale getirmek için kullanılır.
     */
    public static function absoluteUrl(string $path): string
    {
        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        return self::baseUrl() . '/' . ltrim($path, '/');
    }

    /**
     * HTML/script etiketlerini temizler, boşlukları sadeleştirir ve
     * çok-baytlı (Türkçe) karakterlere duyarlı şekilde güvenli bir uzunlukta
     * keser. Kontrolsüz uzun DB metinlerinin meta description'a aynen
     * basılmasını engellemek için TÜM açıklama değerleri buradan geçmeli.
     */
    public static function truncateDescription(?string $text, int $maxLength = 160): string
    {
        $stripped = strip_tags((string) $text);
        $collapsed = preg_replace('/\s+/u', ' ', $stripped);
        $clean = trim($collapsed ?? $stripped);

        if ($clean === '') {
            return '';
        }

        return mb_strimwidth($clean, 0, $maxLength, '…', 'UTF-8');
    }

    /**
     * JSON-LD script bloğu için güvenli JSON üretir. JSON_UNESCAPED_SLASHES
     * KASITLI OLARAK kullanılmaz: '/' escape'i kapatılırsa, bir ürün adı veya
     * açıklaması "</script>" içerdiğinde script bloğu erken kapanıp
     * script-injection'a yol açabilir.
     */
    public static function jsonLdEncode(array $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        return $json !== false ? $json : '{}';
    }

    /**
     * Sitewide WebSite şeması. Sahte veri içermez (sadece site adı ve URL).
     */
    public static function websiteSchema(array $settings): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => SettingsRepository::siteName($settings),
            'url' => self::baseUrl() . '/',
        ];
    }

    /**
     * LocalBusiness/Florist şeması. Gerçek bir işletme adı girilmeden HİÇBİR
     * şema üretilmez (null döner) — placeholder veri gerçek işletme verisi
     * gibi sunulmaz. Diğer alanlar (telefon, adres, logo) sadece gerçek
     * (placeholder olmayan) değer varsa eklenir.
     */
    public static function localBusinessSchema(array $settings): ?array
    {
        $name = SettingsRepository::isPlaceholder($settings['site_name'] ?? null) ? null : $settings['site_name'];
        if ($name === null) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Florist',
            'name' => $name,
            'url' => self::baseUrl() . '/',
        ];

        $phone = SettingsRepository::isPlaceholder($settings['phone'] ?? null) ? null : $settings['phone'];
        if ($phone !== null) {
            $schema['telephone'] = $phone;
        }

        // addressLocality/addressRegion sabit "İstanbul" değil — adresin
        // kendisinden (site_settings.address, ör. "...Beykoz/İstanbul")
        // çıkarılamayacağı için burada yalnızca doğrulanmış streetAddress
        // metninin tamamı kullanılır; ilçe/il ayrıştırması uydurma veri
        // riski taşıdığından yapılmaz.
        $address = SettingsRepository::isPlaceholder($settings['address'] ?? null) ? null : $settings['address'];
        if ($address !== null) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressCountry' => 'TR',
            ];
        }

        // Harita koordinatları yalnızca admin panelden (site_settings.map_lat/
        // map_lng) gerçek/doğrulanmış bir değer girildiyse eklenir.
        $lat = $settings['map_lat'] ?? null;
        $lng = $settings['map_lng'] ?? null;
        if (!SettingsRepository::isPlaceholder($lat) && !SettingsRepository::isPlaceholder($lng)
            && is_numeric($lat) && is_numeric($lng)
        ) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $lat,
                'longitude' => (float) $lng,
            ];
        }

        $logoPath = $settings['logo_path'] ?? null;
        if (!empty($logoPath)) {
            $absoluteLogo = self::absoluteUrl((string) $logoPath);
            $schema['logo'] = $absoluteLogo;
            $schema['image'] = $absoluteLogo;
        }

        // Not: working_hours serbest metin (ör. "Pzt-Cmt 09:00-19:00") olarak
        // saklanıyor; schema.org'un openingHours mikro-formatı (Mo-Fr 09:00-18:00
        // gibi katı gün kodları) ile uyuşmayabileceğinden GEÇERSİZ structured
        // data üretmemek için buraya kasıtlı olarak dahil edilmiyor.

        return $schema;
    }

    /**
     * Ürün detay sayfası için Product şeması. Görsel yoksa 'image' alanı
     * eklenmez (sahte/mevcut olmayan görsel URL'si üretilmez).
     */
    public static function productSchema(array $product, array $extraImages): array
    {
        $imagePath = null;
        if (!empty($product['main_image'])) {
            $imagePath = (string) $product['main_image'];
        } elseif (!empty($extraImages[0]['image_path'])) {
            $imagePath = (string) $extraImages[0]['image_path'];
        }

        $productUrl = self::canonicalUrl('/urun/' . $product['slug']);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'url' => $productUrl,
        ];

        // Fiyatı "iletişime geçin" durumundaki ürünlerde sahte/0 fiyatlı bir
        // Offer üretilmez — offers alanı tamamen atlanır. Sadece gerçek bir
        // fiyat girildiyse (price_status = 'fixed') Offer eklenir.
        if (($product['price_status'] ?? 'fixed') === 'fixed' && $product['price'] !== null) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'priceCurrency' => 'TRY',
                'price' => number_format((float) $product['price'], 2, '.', ''),
                'availability' => 'https://schema.org/InStock',
                'url' => $productUrl,
            ];
        }

        if (!empty($product['category_name'])) {
            $schema['category'] = $product['category_name'];
        }

        if (!empty($product['description'])) {
            $schema['description'] = self::truncateDescription((string) $product['description'], 500);
        }

        if ($imagePath !== null) {
            $schema['image'] = self::absoluteUrl($imagePath);
        }

        return $schema;
    }

    /**
     * BreadcrumbList şeması. $items: [['name' => '...', 'path' => '/...'], ...]
     */
    public static function breadcrumbSchema(array $items): array
    {
        $listItems = [];
        foreach ($items as $index => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => self::canonicalUrl($item['path']),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }
}
