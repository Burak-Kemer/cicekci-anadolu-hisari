<?php
/**
 * Tekrar kullanılabilir ürün kartı.
 * @var array $product  (products.* + category_name + category_slug)
 */
$cardIsContactPrice = ($product['price_status'] ?? 'fixed') === 'contact' || $product['price'] === null;
$cardPrice = $cardIsContactPrice ? 'Fiyat için iletişime geçin' : number_format((float) $product['price'], 2, ',', '.') . ' ₺';
$cardHasImage = !empty($product['main_image']);
?>
<article class="product-card">
    <a href="/urun/<?php echo htmlspecialchars($product['slug']); ?>" class="product-card__link">
        <div class="product-card__media">
            <?php if ($cardHasImage): ?>
                <img src="<?php echo htmlspecialchars($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy" width="400" height="400">
            <?php else: ?>
                <div class="product-card__media product-card__media--empty" aria-hidden="true">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M12 3c1.8 2 1.8 4.5 0 6.5C10.2 7.5 10.2 5 12 3Z" stroke="currentColor" stroke-width="1.3"/><path d="M12 9.5v11" stroke="currentColor" stroke-width="1.3"/><path d="M7 13c1.7-1.3 4-1.3 5 .5 1-1.8 3.3-1.8 5-.5" stroke="currentColor" stroke-width="1.3"/></svg>
                </div>
            <?php endif; ?>
            <?php if (!empty($product['is_featured'])): ?>
                <span class="product-card__badge">Öne Çıkan</span>
            <?php endif; ?>
        </div>
        <div class="product-card__body">
            <span class="product-card__category"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></span>
            <h3 class="product-card__name"><?php echo htmlspecialchars($product['name']); ?></h3>
            <span class="product-card__price"><?php echo $cardPrice; ?></span>
        </div>
    </a>
</article>
