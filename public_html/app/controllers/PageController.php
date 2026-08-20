<?php

declare(strict_types=1);

class PageController
{
    public static function about(array $settings): void
    {
        $siteName = SettingsRepository::siteName($settings);
        $pageTitle = 'Hakkımızda | ' . $siteName;

        $rawDescription = SettingsRepository::isPlaceholder($settings['default_meta_description'] ?? null)
            ? 'Anadolu Hisarı\'nda çiçekçilik anlayışımız, kalite yaklaşımımız ve özel tasarımlarımız hakkında.'
            : (string) $settings['default_meta_description'];
        $metaDescription = Seo::truncateDescription($rawDescription, 160);

        $canonicalPath = '/hakkimizda';
        $ogType = 'website';
        $ogImage = !empty($settings['logo_path']) ? Seo::absoluteUrl((string) $settings['logo_path']) : null;

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/pages/about.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public static function contact(array $settings): void
    {
        $siteName = SettingsRepository::siteName($settings);
        $pageTitle = 'İletişim | ' . $siteName;

        $rawDescription = SettingsRepository::isPlaceholder($settings['default_meta_description'] ?? null)
            ? 'Telefon ve WhatsApp üzerinden bize ulaşın. Anadolu Hisarı ve çevresine hizmet veriyoruz.'
            : (string) $settings['default_meta_description'];
        $metaDescription = Seo::truncateDescription($rawDescription, 160);

        $canonicalPath = '/iletisim';
        $ogType = 'website';
        $ogImage = !empty($settings['logo_path']) ? Seo::absoluteUrl((string) $settings['logo_path']) : null;

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/pages/contact.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
