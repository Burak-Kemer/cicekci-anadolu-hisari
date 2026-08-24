</main>

<?php
$footerSiteName = SettingsRepository::siteName($settings);
$footerTagline = SettingsRepository::isPlaceholder($settings['site_tagline'] ?? null) ? null : $settings['site_tagline'];
$footerPhoneRaw = $settings['phone'] ?? null;
$footerHasPhone = !SettingsRepository::isPlaceholder($footerPhoneRaw);
$footerPhoneDigits = $footerHasPhone ? preg_replace('/[^0-9+]/', '', (string) $footerPhoneRaw) : null;
$footerWhatsappLink = SettingsRepository::whatsAppLink(
    $settings['whatsapp'] ?? null,
    'Merhaba, web sitenizden ulaşıyorum. Bilgi almak istiyorum.'
);
$footerEmail = SettingsRepository::isPlaceholder($settings['email'] ?? null) ? null : $settings['email'];
$footerAddress = SettingsRepository::isPlaceholder($settings['address'] ?? null) ? null : $settings['address'];
$footerHours = SettingsRepository::isPlaceholder($settings['working_hours'] ?? null) ? null : $settings['working_hours'];
$footerInstagram = SettingsRepository::isPlaceholder($settings['instagram_url'] ?? null) ? null : $settings['instagram_url'];
$footerFacebook = SettingsRepository::isPlaceholder($settings['facebook_url'] ?? null) ? null : $settings['facebook_url'];
$footerTelegram = SettingsRepository::isPlaceholder($settings['telegram_url'] ?? null) ? null : $settings['telegram_url'];
?>

<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <span class="site-footer__logo"><?php echo htmlspecialchars($footerSiteName); ?></span>
            <?php if ($footerTagline !== null): ?>
                <p class="site-footer__tagline"><?php echo htmlspecialchars($footerTagline); ?></p>
            <?php endif; ?>
            <?php if ($footerInstagram !== null || $footerFacebook !== null || $footerTelegram !== null): ?>
            <div class="site-footer__social">
                <?php if ($footerInstagram !== null): ?>
                    <a href="<?php echo htmlspecialchars($footerInstagram); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><circle cx="17.2" cy="6.8" r="1" fill="currentColor"/></svg>
                    </a>
                <?php endif; ?>
                <?php if ($footerFacebook !== null): ?>
                    <a href="<?php echo htmlspecialchars($footerFacebook); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14 8.5h2V5.3h-2c-2 0-3.5 1.6-3.5 3.6v1.6H8.5v3h2V21h3v-7.5h2.2l.5-3H13.5V9c0-.3.2-.5.5-.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                    </a>
                <?php endif; ?>
                <?php if ($footerTelegram !== null): ?>
                    <a href="<?php echo htmlspecialchars($footerTelegram); ?>" target="_blank" rel="noopener" aria-label="Telegram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 4 3 11.5l6 2M21 4 15.5 20l-6.5-6.5M21 4l-11.5 9.5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"/></svg>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <nav class="site-footer__links" aria-label="Hızlı bağlantılar">
            <span class="site-footer__heading">Hızlı Bağlantılar</span>
            <a href="/">Ana Sayfa</a>
            <a href="/cicekler">Çiçeklerimiz</a>
            <a href="/hakkimizda">Hakkımızda</a>
            <a href="/iletisim">İletişim</a>
        </nav>

        <div class="site-footer__contact">
            <span class="site-footer__heading">İletişim</span>
            <?php if ($footerHasPhone): ?>
                <a href="tel:<?php echo htmlspecialchars((string) $footerPhoneDigits); ?>"><?php echo htmlspecialchars((string) $footerPhoneRaw); ?></a>
            <?php endif; ?>
            <?php if ($footerWhatsappLink !== null): ?>
                <a href="<?php echo htmlspecialchars($footerWhatsappLink); ?>" target="_blank" rel="noopener">WhatsApp'tan Yazın</a>
            <?php endif; ?>
            <?php if ($footerEmail !== null): ?>
                <a href="mailto:<?php echo htmlspecialchars($footerEmail); ?>"><?php echo htmlspecialchars($footerEmail); ?></a>
            <?php endif; ?>
            <?php if ($footerAddress !== null): ?>
                <span><?php echo nl2br(htmlspecialchars($footerAddress)); ?></span>
            <?php endif; ?>
            <?php if ($footerHours !== null): ?>
                <span><?php echo htmlspecialchars($footerHours); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="site-footer__bottom container">
        <span>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($footerSiteName); ?>. Tüm hakları saklıdır.</span>
    </div>
</footer>

<?php if ($footerHasPhone || $footerWhatsappLink !== null): ?>
<div class="mobile-sticky-cta" role="region" aria-label="Hızlı iletişim">
    <?php if ($footerWhatsappLink !== null): ?>
        <a href="<?php echo htmlspecialchars($footerWhatsappLink); ?>" class="mobile-sticky-cta__btn mobile-sticky-cta__btn--whatsapp" target="_blank" rel="noopener">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3Z" stroke="currentColor" stroke-width="1.6"/></svg>
            WhatsApp
        </a>
    <?php endif; ?>
    <?php if ($footerHasPhone): ?>
        <a href="tel:<?php echo htmlspecialchars((string) $footerPhoneDigits); ?>" class="mobile-sticky-cta__btn mobile-sticky-cta__btn--call">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.2 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.4 21 3 13.6 3 4.7c0-.6.4-1 1-1h3.4c.6 0 1 .4 1 1 0 1.3.2 2.6.6 3.8.1.4 0 .8-.2 1.1L6.6 10.8Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
            Ara
        </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php
// JSON-LD: sitewide WebSite + (varsa) LocalBusiness her sayfada, sayfaya
// özel şemalar ($jsonLd — Product/BreadcrumbList vb.) varsa üstüne eklenir.
// localBusinessSchema() gerçek işletme adı girilmeden null döner; bu durumda
// hiçbir placeholder veri şemaya karışmaz.
$jsonLdBlocks = [Seo::websiteSchema($settings)];
$localBusiness = Seo::localBusinessSchema($settings);
if ($localBusiness !== null) {
    $jsonLdBlocks[] = $localBusiness;
}
foreach (($jsonLd ?? []) as $extraSchema) {
    if ($extraSchema !== null) {
        $jsonLdBlocks[] = $extraSchema;
    }
}
foreach ($jsonLdBlocks as $schemaBlock):
?>
<script type="application/ld+json"><?php echo Seo::jsonLdEncode($schemaBlock); ?></script>
<?php endforeach; ?>

<script src="/assets/js/public.js"></script>
</body>
</html>
