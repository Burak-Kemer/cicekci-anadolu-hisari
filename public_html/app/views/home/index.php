<?php
/**
 * @var array $featuredProducts
 * @var array $categories
 * @var array $galleryPreview
 * @var array<string, string|null> $settings
 */
$homeSiteName = SettingsRepository::siteName($settings);
$homeTagline = SettingsRepository::isPlaceholder($settings['site_tagline'] ?? null) ? null : $settings['site_tagline'];
$homeWaLink = SettingsRepository::whatsAppLink(
    $settings['whatsapp'] ?? null,
    'Merhaba, web sitenizden ulaşıyorum. Bilgi almak istiyorum.'
);
?>
<section class="hero hero--home">
    <div class="hero__decor" aria-hidden="true">
        <svg viewBox="0 0 400 400" class="botanical-line botanical-line--hero" fill="none">
            <path d="M200 40 C 130 100, 130 220, 200 300 C 270 220, 270 100, 200 40 Z" stroke="currentColor" stroke-width="1.2"/>
            <path d="M200 300 L200 380" stroke="currentColor" stroke-width="1.2"/>
            <path d="M200 340 C 160 340, 130 355, 105 380" stroke="currentColor" stroke-width="1"/>
            <path d="M200 320 C 240 320, 270 335, 295 360" stroke="currentColor" stroke-width="1"/>
            <path d="M200 120 C 175 95, 170 60, 185 25" stroke="currentColor" stroke-width="1"/>
            <path d="M200 120 C 225 95, 230 60, 215 25" stroke="currentColor" stroke-width="1"/>
        </svg>
    </div>
    <div class="container hero__content">
        <span class="eyebrow eyebrow--on-dark">Anadolu Hisarı</span>
        <h1 class="hero__title"><?php echo htmlspecialchars($homeSiteName); ?></h1>
        <p class="hero__lede hero__lede--on-dark">
            <?php echo $homeTagline !== null ? htmlspecialchars($homeTagline) : 'Anadolu Hisarı ve çevresine, özenle hazırlanan taze çiçekler ve özel tasarımlar.'; ?>
        </p>
        <div class="hero__actions">
            <a href="/cicekler" class="btn btn--on-dark">Çiçekleri İncele</a>
            <a href="/iletisim" class="btn btn--outline-inverse">İletişime Geç</a>
        </div>
    </div>
</section>

<?php if (!empty($featuredProducts)): ?>
<section class="section section--tinted">
    <div class="container">
        <div class="section__heading">
            <span class="eyebrow">Seçkimiz</span>
            <h2 class="section__title">Öne Çıkan Çiçekler</h2>
        </div>
        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <?php require __DIR__ . '/../partials/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        <div class="section__footer">
            <a href="/cicekler" class="btn btn--outline">Tüm Çiçekleri Gör</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($categories)): ?>
<section class="section">
    <div class="container">
        <div class="section__heading">
            <span class="eyebrow">Kategoriler</span>
            <h2 class="section__title">İhtiyacınıza Göre Seçin</h2>
        </div>
        <div class="category-tile-grid">
            <?php foreach ($categories as $category): ?>
                <a href="/cicekler/<?php echo htmlspecialchars($category['slug']); ?>" class="category-tile">
                    <span class="category-tile__name"><?php echo htmlspecialchars($category['name']); ?></span>
                    <span class="category-tile__arrow" aria-hidden="true">→</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section split">
    <div class="container split__inner split__inner--reverse">
        <div class="split__accent" aria-hidden="true">
            <svg viewBox="0 0 200 240" class="botanical-line" fill="none">
                <path d="M100 20 C 60 60, 60 120, 100 160 C 140 120, 140 60, 100 20 Z" stroke="currentColor" stroke-width="1.4"/>
                <path d="M100 160 L100 220" stroke="currentColor" stroke-width="1.4"/>
                <path d="M100 190 C 80 190, 65 200, 55 215" stroke="currentColor" stroke-width="1.2"/>
                <path d="M100 175 C 120 175, 135 185, 145 200" stroke="currentColor" stroke-width="1.2"/>
            </svg>
        </div>
        <div class="split__text">
            <span class="eyebrow">Hikayemiz</span>
            <h2>Çiçekle Anlatılan Bir Zanaat</h2>
            <p>Her aranjmanı, alacağı kişiye ulaştığı anı düşünerek hazırlıyoruz. Taze malzeme, doğru kompozisyon ve özenli işçilik — bizim için çiçekçilik böyle bir zanaat.</p>
            <a href="/hakkimizda" class="link-arrow">Hikayemizin devamı →</a>
        </div>
    </div>
</section>

<?php if (!empty($galleryPreview)): ?>
<section class="section section--tinted">
    <div class="container">
        <div class="section__heading">
            <span class="eyebrow">Galeri</span>
            <h2 class="section__title">Çalışmalarımızdan</h2>
        </div>
        <div class="gallery-preview-grid">
            <?php foreach ($galleryPreview as $index => $image): ?>
                <div class="gallery-preview-grid__item<?php echo $index === 0 ? ' gallery-preview-grid__item--wide' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars((string) ($image['alt_text'] ?? '')); ?>" loading="lazy">
                </div>
            <?php endforeach; ?>
        </div>
        <div class="section__footer">
            <a href="/galeri" class="btn btn--outline">Tüm Galeriyi Gör</a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="section__heading">
            <span class="eyebrow">Neden Biz</span>
            <h2 class="section__title">Neden Bizi Tercih Etmelisiniz?</h2>
        </div>
        <div class="feature-list feature-list--grid">
            <div class="feature-list__item">
                <span class="feature-list__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 3c1.8 2 1.8 4.5 0 6.5C10.2 7.5 10.2 5 12 3Z" stroke="currentColor" stroke-width="1.3"/><path d="M12 9.5v11" stroke="currentColor" stroke-width="1.3"/></svg>
                </span>
                <h3>Özenle Hazırlanan Çiçekler</h3>
                <p>Her sipariş tek tek elden geçer, standart üretim değildir.</p>
            </div>
            <div class="feature-list__item">
                <span class="feature-list__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M4 12l6 6L20 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <h3>Taze ve Kaliteli Ürünler</h3>
                <p>Çiçeklerimiz canlılığını koruyacak şekilde özenle seçilir.</p>
            </div>
            <div class="feature-list__item">
                <span class="feature-list__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 2v4M12 18v4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M2 12h4M18 12h4M4.9 19.1l2.8-2.8M16.3 7.7l2.8-2.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                </span>
                <h3>Özel Tasarımlar</h3>
                <p>İsteğinize göre şekillenen kişiselleştirilmiş kompozisyonlar.</p>
            </div>
            <div class="feature-list__item">
                <span class="feature-list__icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-6.3 7-11.5A7 7 0 0 0 5 9.5C5 14.7 12 21 12 21Z" stroke="currentColor" stroke-width="1.4"/><circle cx="12" cy="9.5" r="2.3" stroke="currentColor" stroke-width="1.4"/></svg>
                </span>
                <h3>Anadolu Hisarı ve Çevresine Hizmet</h3>
                <p>Bölgenize yakın, güvenilir ve hızlı hizmet.</p>
            </div>
        </div>
    </div>
</section>

<section class="section cta-band">
    <div class="container cta-band__inner">
        <h2>Bir Çiçek İçin Bizimle İletişime Geçin</h2>
        <p>Beğendiğiniz ürün hakkında bilgi almak için telefon veya WhatsApp üzerinden ulaşabilirsiniz.</p>
        <div class="cta-band__actions">
            <?php if ($homeWaLink !== null): ?>
                <a href="<?php echo htmlspecialchars($homeWaLink); ?>" class="btn btn--whatsapp" target="_blank" rel="noopener">WhatsApp'tan Yazın</a>
            <?php endif; ?>
            <a href="/iletisim" class="btn btn--on-dark">İletişim Bilgileri</a>
        </div>
    </div>
</section>
