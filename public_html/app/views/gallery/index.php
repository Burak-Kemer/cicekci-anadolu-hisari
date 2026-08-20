<?php
/** @var array $images */
?>
<section class="section hero hero--page">
    <div class="container">
        <span class="eyebrow">Galeri</span>
        <h1>Çalışmalarımızdan Kareler</h1>
        <p class="hero__lede">Hazırladığımız buket ve aranjmanlardan bir seçki.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($images)): ?>
            <div class="empty-state">
                <p>Galeri görselleri yakında eklenecektir.</p>
                <a href="/cicekler" class="btn btn--outline">Çiçeklerimize Göz Atın</a>
            </div>
        <?php else: ?>
            <div class="gallery-grid" id="galleryGrid">
                <?php foreach ($images as $index => $image): ?>
                    <button type="button" class="gallery-grid__item" data-lightbox-trigger data-index="<?php echo (int) $index; ?>">
                        <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars((string) ($image['alt_text'] ?? '')); ?>" loading="lazy">
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Görsel galerisi" aria-hidden="true">
                <button type="button" class="lightbox__close" id="lightboxClose" aria-label="Kapat">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M5 5l14 14M19 5 5 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
                <button type="button" class="lightbox__nav lightbox__nav--prev" id="lightboxPrev" aria-label="Önceki görsel">‹</button>
                <figure class="lightbox__figure">
                    <img id="lightboxImage" src="" alt="">
                    <figcaption id="lightboxCaption"></figcaption>
                </figure>
                <button type="button" class="lightbox__nav lightbox__nav--next" id="lightboxNext" aria-label="Sonraki görsel">›</button>
            </div>

            <script type="application/json" id="galleryData">
                <?php
                $galleryData = array_map(static function ($image) {
                    return [
                        'src' => $image['image_path'],
                        'alt' => (string) ($image['alt_text'] ?? ''),
                        'caption' => (string) ($image['caption'] ?? ''),
                    ];
                }, $images);
                // JSON_UNESCAPED_SLASHES kasıtlı olarak KULLANILMIYOR: '/' kaçışının
                // kapatılması, alt_text/caption içinde "</script>" geçerse script
                // bloğunun erken kapanmasına (XSS) yol açabilirdi.
                echo json_encode($galleryData, JSON_UNESCAPED_UNICODE);
                ?>
            </script>
        <?php endif; ?>
    </div>
</section>
