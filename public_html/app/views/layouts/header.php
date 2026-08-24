<?php
/** @var array<string, string|null> $settings */
/** @var string $pageTitle */
/** @var string $metaDescription */

$siteName = SettingsRepository::siteName($settings);
$logoPath = $settings['logo_path'] ?? null;
$phoneRaw = $settings['phone'] ?? null;
$whatsappRaw = $settings['whatsapp'] ?? null;
$hasPhone = !SettingsRepository::isPlaceholder($phoneRaw);
$phoneDigits = $hasPhone ? preg_replace('/[^0-9+]/', '', (string) $phoneRaw) : null;
$whatsappQuickLink = SettingsRepository::whatsAppLink(
    $whatsappRaw,
    'Merhaba, web sitenizden ulaşıyorum. Bilgi almak istiyorum.'
);

$navItems = [
    '/' => 'Ana Sayfa',
    '/cicekler' => 'Çiçeklerimiz',
    '/hakkimizda' => 'Hakkımızda',
    '/iletisim' => 'İletişim',
];
$currentPath = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
if ($currentPath === '') {
    $currentPath = '/';
}

// SEO: her controller bu değişkenleri set edebilir; hiçbiri zorunlu değil,
// makul varsayılanlarla geriye dönük uyumlu (Aşama 7-11'de sadece
// $pageTitle/$metaDescription set ediliyordu, hâlâ öyle çalışır).
$canonicalUrl = Seo::canonicalUrl($canonicalPath ?? $currentPath);
$robotsContent = ($noindex ?? false) ? 'noindex, nofollow' : 'index, follow';
$ogTypeValue = $ogType ?? 'website';
$ogImageUrl = $ogImage ?? null;
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <?php if ($metaDescription !== ''): ?>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <?php endif; ?>
    <meta name="robots" content="<?php echo htmlspecialchars($robotsContent); ?>">
    <?php if (!($noindex ?? false)): ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <?php endif; ?>

    <meta property="og:type" content="<?php echo htmlspecialchars($ogTypeValue); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <?php if ($metaDescription !== ''): ?>
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <?php endif; ?>
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta property="og:locale" content="tr_TR">
    <?php if ($ogImageUrl !== null): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImageUrl); ?>">
    <?php endif; ?>

    <meta name="twitter:card" content="<?php echo $ogImageUrl !== null ? 'summary_large_image' : 'summary'; ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <?php if ($metaDescription !== ''): ?>
    <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <?php endif; ?>
    <?php if ($ogImageUrl !== null): ?>
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImageUrl); ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500&family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/public.css">
</head>
<body>
<a href="#main-content" class="skip-link">İçeriğe geç</a>

<header class="site-header" id="siteHeader">
    <div class="site-header__inner container">
        <a href="/" class="site-header__logo">
            <?php if ($logoPath !== null && $logoPath !== ''): ?>
                <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="site-header__logo-img">
            <?php else: ?>
                <span class="site-header__logo-text"><?php echo htmlspecialchars($siteName); ?></span>
            <?php endif; ?>
        </a>

        <nav class="site-header__nav" aria-label="Ana menü">
            <ul class="site-header__nav-list">
                <?php foreach ($navItems as $href => $label): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($href); ?>" class="site-header__nav-link<?php echo $currentPath === $href ? ' is-active' : ''; ?>">
                            <?php echo htmlspecialchars($label); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="site-header__actions">
            <?php if ($hasPhone): ?>
                <a href="tel:<?php echo htmlspecialchars((string) $phoneDigits); ?>" class="site-header__contact-link" aria-label="Bizi arayın">
                    <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.2 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.4 21 3 13.6 3 4.7c0-.6.4-1 1-1h3.4c.6 0 1 .4 1 1 0 1.3.2 2.6.6 3.8.1.4 0 .8-.2 1.1L6.6 10.8Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    <span class="site-header__contact-label">Ara</span>
                </a>
            <?php endif; ?>
            <?php if ($whatsappQuickLink !== null): ?>
                <a href="<?php echo htmlspecialchars($whatsappQuickLink); ?>" class="site-header__contact-link site-header__contact-link--whatsapp" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın">
                    <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3Z" stroke="currentColor" stroke-width="1.6"/><path d="M8.5 8.7c.2-.5.5-.5.8-.5h.6c.2 0 .4 0 .6.4.2.5.6 1.6.7 1.7.1.1.1.3 0 .5-.1.2-.2.3-.3.5-.2.2-.3.3-.1.6.2.3.8 1.2 1.7 1.9 1.1.9 1.9 1.2 2.2 1.3.3.1.5.1.6-.1.2-.2.7-.8.9-1 .2-.2.3-.2.6-.1.2.1 1.5.7 1.8.8.3.1.4.2.5.3.1.2.1.9-.2 1.6-.3.7-1.6 1.4-2.2 1.4-.6 0-1.3.1-4.3-1.2-3.6-1.6-5.9-5.3-6.1-5.6-.2-.3-1.4-1.9-1.4-3.6 0-1.7.9-2.5 1.2-2.9Z" fill="currentColor"/></svg>
                    <span class="site-header__contact-label">WhatsApp</span>
                </a>
            <?php endif; ?>

            <button type="button" class="site-header__menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="mobileDrawer" aria-label="Menüyü aç">
                <span class="hamburger"></span>
            </button>
        </div>
    </div>
</header>

<div class="mobile-drawer" id="mobileDrawer" aria-hidden="true">
    <div class="mobile-drawer__backdrop" id="drawerBackdrop" tabindex="-1"></div>
    <div class="mobile-drawer__panel" role="dialog" aria-modal="true" aria-label="Site menüsü">
        <div class="mobile-drawer__top">
            <span class="mobile-drawer__brand"><?php echo htmlspecialchars($siteName); ?></span>
            <button type="button" class="mobile-drawer__close" id="drawerClose" aria-label="Menüyü kapat">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 5l14 14M19 5 5 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>
        </div>

        <nav class="mobile-drawer__nav" aria-label="Mobil menü">
            <?php foreach ($navItems as $href => $label): ?>
                <a href="<?php echo htmlspecialchars($href); ?>" class="mobile-drawer__link<?php echo $currentPath === $href ? ' is-active' : ''; ?>">
                    <?php echo htmlspecialchars($label); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($hasPhone || $whatsappQuickLink !== null): ?>
        <div class="mobile-drawer__contact">
            <?php if ($whatsappQuickLink !== null): ?>
                <a href="<?php echo htmlspecialchars($whatsappQuickLink); ?>" class="btn btn--whatsapp btn--block" target="_blank" rel="noopener">WhatsApp'tan Yazın</a>
            <?php endif; ?>
            <?php if ($hasPhone): ?>
                <a href="tel:<?php echo htmlspecialchars((string) $phoneDigits); ?>" class="btn btn--outline-inverse btn--block">Hemen Ara</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<main id="main-content">
