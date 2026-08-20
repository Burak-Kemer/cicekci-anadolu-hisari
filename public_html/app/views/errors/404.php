<?php
/** @var array<string, string|null> $settings */

$pageTitle = 'Sayfa Bulunamadı — ' . SettingsRepository::siteName($settings);
$metaDescription = '';
$noindex = true;

require __DIR__ . '/../layouts/header.php';
?>
<section class="section section--narrow error-page">
    <div class="container error-page__inner">
        <span class="error-page__code">404</span>
        <h1>Aradığınız sayfa bulunamadı</h1>
        <p>Bağlantı hatalı olabilir veya sayfa kaldırılmış olabilir. Çiçek kataloğumuza göz atabilir ya da ana sayfaya dönebilirsiniz.</p>
        <div class="error-page__actions">
            <a href="/" class="btn btn--primary">Ana Sayfaya Dön</a>
            <a href="/cicekler" class="btn btn--outline">Çiçeklerimizi Keşfedin</a>
        </div>
    </div>
</section>
<?php
require __DIR__ . '/../layouts/footer.php';
