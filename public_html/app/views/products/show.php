<?php
/**
 * @var array $product
 * @var array $images
 * @var array $similarProducts
 * @var array<string, string|null> $settings
 */

$isContactPrice = ($product['price_status'] ?? 'fixed') === 'contact' || $product['price'] === null;
$formattedPrice = $isContactPrice ? 'Fiyat için iletişime geçin' : number_format((float) $product['price'], 2, ',', '.') . ' ₺';

$allImages = [];
if (!empty($product['main_image'])) {
    $allImages[] = ['image_path' => $product['main_image'], 'alt_text' => $product['name']];
}
foreach ($images as $extraImage) {
    $allImages[] = $extraImage;
}

$waMessage = $isContactPrice
    ? 'Merhaba, web sitenizdeki ' . $product['name'] . ' için fiyat bilgisi almak istiyorum.'
    : 'Merhaba, web sitenizdeki ' . $product['name'] . ' hakkında bilgi almak istiyorum. Web sitesinde görünen fiyat: ' . $formattedPrice . '.';
$waLink = SettingsRepository::whatsAppLink($settings['whatsapp'] ?? null, $waMessage);

$pPhoneRaw = $settings['phone'] ?? null;
$pHasPhone = !SettingsRepository::isPlaceholder($pPhoneRaw);
$pPhoneDigits = $pHasPhone ? preg_replace('/[^0-9+]/', '', (string) $pPhoneRaw) : null;
?>
<nav class="breadcrumb container" aria-label="Sayfa yolu">
    <a href="/">Ana Sayfa</a>
    <span aria-hidden="true">/</span>
    <a href="/cicekler">Çiçeklerimiz</a>
    <span aria-hidden="true">/</span>
    <a href="/cicekler/<?php echo htmlspecialchars($product['category_slug']); ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
    <span aria-hidden="true">/</span>
    <span aria-current="page"><?php echo htmlspecialchars($product['name']); ?></span>
</nav>

<section class="section section--top-tight product-detail">
    <div class="container product-detail__grid">
        <div class="product-gallery">
            <?php if (empty($allImages)): ?>
                <div class="product-gallery__empty" aria-hidden="true">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none"><path d="M12 3c1.8 2 1.8 4.5 0 6.5C10.2 7.5 10.2 5 12 3Z" stroke="currentColor" stroke-width="1.2"/><path d="M12 9.5v11" stroke="currentColor" stroke-width="1.2"/><path d="M7 13c1.7-1.3 4-1.3 5 .5 1-1.8 3.3-1.8 5-.5" stroke="currentColor" stroke-width="1.2"/></svg>
                </div>
            <?php else: ?>
                <div class="product-gallery__track" id="productGalleryTrack"<?php echo count($allImages) > 1 ? ' tabindex="0" role="region" aria-label="Ürün görselleri, sağa/sola kaydırın"' : ''; ?>>
                    <?php foreach ($allImages as $imgIndex => $img): ?>
                        <div class="product-gallery__slide">
                            <?php if ($imgIndex === 0): ?>
                            <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="<?php echo htmlspecialchars((string) ($img['alt_text'] ?? $product['name'])); ?>" loading="eager" fetchpriority="high">
                            <?php else: ?>
                            <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="<?php echo htmlspecialchars((string) ($img['alt_text'] ?? $product['name'])); ?>" loading="lazy">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($allImages) > 1): ?>
                <div class="product-gallery__dots" id="productGalleryDots">
                    <?php foreach ($allImages as $i => $img): ?>
                        <span class="product-gallery__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="product-detail__info">
            <span class="product-detail__category"><?php echo htmlspecialchars($product['category_name']); ?></span>
            <h1 class="product-detail__name"><?php echo htmlspecialchars($product['name']); ?></h1>
            <span class="product-detail__price"><?php echo $formattedPrice; ?></span>

            <?php if (!empty($product['description'])): ?>
                <p class="product-detail__description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <?php endif; ?>

            <div class="product-detail__cta">
                <?php if ($waLink !== null): ?>
                    <a href="<?php echo htmlspecialchars($waLink); ?>" class="btn btn--whatsapp btn--large btn--block" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3Z" stroke="currentColor" stroke-width="1.6"/></svg>
                        Bu Çiçek Hakkında Bilgi Al
                    </a>
                <?php elseif ($pHasPhone): ?>
                    <a href="tel:<?php echo htmlspecialchars((string) $pPhoneDigits); ?>" class="btn btn--primary btn--large btn--block">
                        Bu Çiçek Hakkında Bilgi Al
                    </a>
                <?php else: ?>
                    <a href="/iletisim" class="btn btn--primary btn--large btn--block">Bu Çiçek Hakkında Bilgi Al</a>
                <?php endif; ?>
                <p class="product-detail__cta-note">Online sipariş alınmamaktadır — bilgi almak için doğrudan iletişime geçin.</p>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($similarProducts)): ?>
<section class="section section--tinted">
    <div class="container">
        <h2 class="section__title">Benzer Çiçekler</h2>
        <div class="product-grid">
            <?php foreach ($similarProducts as $similarProduct): $product = $similarProduct; ?>
                <?php require __DIR__ . '/../partials/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
