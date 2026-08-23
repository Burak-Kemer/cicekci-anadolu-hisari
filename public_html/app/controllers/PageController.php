<?php

declare(strict_types=1);

class PageController
{
    public static function about(array $settings): void
    {
        $siteName = SettingsRepository::siteName($settings);
        $pageTitle = 'Hakkımızda | ' . $siteName;

        $rawDescription = 'Beykoz Göksu\'da tasarım odaklı bir çiçek stüdyosu: ' . $siteName . '\'ı tanıyın.';
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
        $pageTitle = 'İletişim | ' . $siteName . ' Göksu Çiçekçi';

        $rawDescription = $siteName . '\'a Beykoz Göksu\'daki stüdyomuzdan, telefon, WhatsApp veya Telegram üzerinden ulaşın.';
        $metaDescription = Seo::truncateDescription($rawDescription, 160);

        $canonicalPath = '/iletisim';
        $ogType = 'website';
        $ogImage = !empty($settings['logo_path']) ? Seo::absoluteUrl((string) $settings['logo_path']) : null;

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/pages/contact.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
