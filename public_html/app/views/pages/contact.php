<?php
/** @var array<string, string|null> $settings */

$cPhoneRaw = $settings['phone'] ?? null;
$cHasPhone = !SettingsRepository::isPlaceholder($cPhoneRaw);
$cPhoneDigits = $cHasPhone ? preg_replace('/[^0-9+]/', '', (string) $cPhoneRaw) : null;
$cWhatsappLink = SettingsRepository::whatsAppLink(
    $settings['whatsapp'] ?? null,
    'Merhaba, web sitenizden ulaşıyorum. Bilgi almak istiyorum.'
);
$cEmail = SettingsRepository::isPlaceholder($settings['email'] ?? null) ? null : $settings['email'];
$cAddress = SettingsRepository::isPlaceholder($settings['address'] ?? null) ? null : $settings['address'];
$cHours = SettingsRepository::isPlaceholder($settings['working_hours'] ?? null) ? null : $settings['working_hours'];
$cHasMap = $cAddress !== null;
?>
<section class="section hero hero--page">
    <div class="container">
        <span class="eyebrow">İletişim</span>
        <h1>Bize Ulaşın</h1>
        <p class="hero__lede">Sorularınız ve çiçek talepleriniz için telefon veya WhatsApp üzerinden doğrudan iletişime geçebilirsiniz.</p>
    </div>
</section>

<section class="section contact-layout">
    <div class="container contact-layout__inner">
        <div class="contact-card-list">
            <?php if ($cHasPhone): ?>
            <a href="tel:<?php echo htmlspecialchars((string) $cPhoneDigits); ?>" class="contact-card">
                <span class="contact-card__icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.2 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.4 21 3 13.6 3 4.7c0-.6.4-1 1-1h3.4c.6 0 1 .4 1 1 0 1.3.2 2.6.6 3.8.1.4 0 .8-.2 1.1L6.6 10.8Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                </span>
                <span class="contact-card__label">Telefon</span>
                <span class="contact-card__value"><?php echo htmlspecialchars((string) $cPhoneRaw); ?></span>
            </a>
            <?php endif; ?>

            <?php if ($cWhatsappLink !== null): ?>
            <a href="<?php echo htmlspecialchars($cWhatsappLink); ?>" class="contact-card" target="_blank" rel="noopener">
                <span class="contact-card__icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3Z" stroke="currentColor" stroke-width="1.6"/></svg>
                </span>
                <span class="contact-card__label">WhatsApp</span>
                <span class="contact-card__value">Hemen Yazın</span>
            </a>
            <?php endif; ?>

            <?php if ($cEmail !== null): ?>
            <a href="mailto:<?php echo htmlspecialchars($cEmail); ?>" class="contact-card">
                <span class="contact-card__icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.5"/></svg>
                </span>
                <span class="contact-card__label">E-posta</span>
                <span class="contact-card__value"><?php echo htmlspecialchars($cEmail); ?></span>
            </a>
            <?php endif; ?>

            <?php if ($cAddress !== null || $cHours !== null): ?>
            <div class="contact-card contact-card--static">
                <span class="contact-card__icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-6.3 7-11.5A7 7 0 0 0 5 9.5C5 14.7 12 21 12 21Z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="9.5" r="2.3" stroke="currentColor" stroke-width="1.5"/></svg>
                </span>
                <span class="contact-card__label">Adres &amp; Çalışma Saatleri</span>
                <?php if ($cAddress !== null): ?><span class="contact-card__value"><?php echo nl2br(htmlspecialchars($cAddress)); ?></span><?php endif; ?>
                <?php if ($cHours !== null): ?><span class="contact-card__value contact-card__value--muted"><?php echo htmlspecialchars($cHours); ?></span><?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!$cHasPhone && $cWhatsappLink === null && $cEmail === null && $cAddress === null): ?>
            <div class="contact-card contact-card--static">
                <span class="contact-card__value">İletişim bilgileri yakında eklenecektir.</span>
            </div>
            <?php endif; ?>
        </div>

        <div class="contact-map">
            <?php if ($cHasMap): ?>
                <iframe
                    src="https://www.google.com/maps?q=<?php echo urlencode((string) $cAddress); ?>&output=embed"
                    class="contact-map__frame"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Konum haritası">
                </iframe>
            <?php else: ?>
                <div class="contact-map__placeholder">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-6.3 7-11.5A7 7 0 0 0 5 9.5C5 14.7 12 21 12 21Z" stroke="currentColor" stroke-width="1.4"/><circle cx="12" cy="9.5" r="2.3" stroke="currentColor" stroke-width="1.4"/></svg>
                    <p>Google Haritalar konumu, işletme adresi belirlendiğinde burada görüntülenecektir.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
