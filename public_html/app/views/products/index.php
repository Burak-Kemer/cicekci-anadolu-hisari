<?php
/**
 * @var array $products
 * @var array $categories
 * @var array|null $activeCategory
 */
?>
<section class="section hero hero--page">
    <div class="container">
        <span class="eyebrow">Katalog</span>
        <h1><?php echo $activeCategory !== null ? htmlspecialchars($activeCategory['name']) : 'Çiçeklerimiz'; ?></h1>
        <p class="hero__lede">Taze ve özenle hazırlanan çiçeklerimizi inceleyin, beğendiğiniz ürün hakkında doğrudan bize ulaşın.</p>
    </div>
</section>

<?php if (!empty($categories)): ?>
<div class="category-chip-bar">
    <div class="category-chip-scroll" role="navigation" aria-label="Kategori filtresi">
        <a href="/cicekler" class="category-chip<?php echo $activeCategory === null ? ' is-active' : ''; ?>">Tümü</a>
        <?php foreach ($categories as $category): ?>
            <a href="/cicekler/<?php echo htmlspecialchars($category['slug']); ?>" class="category-chip<?php echo ($activeCategory !== null && $activeCategory['id'] === $category['id']) ? ' is-active' : ''; ?>">
                <?php echo htmlspecialchars($category['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<section class="section section--top-tight">
    <div class="container">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <p><?php echo $activeCategory !== null ? 'Bu kategoride şu anda ürün bulunmuyor.' : 'Şu anda listelenecek ürün bulunmuyor.'; ?></p>
                <a href="/cicekler" class="btn btn--outline">Tüm Çiçekleri Gör</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php require __DIR__ . '/../partials/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
