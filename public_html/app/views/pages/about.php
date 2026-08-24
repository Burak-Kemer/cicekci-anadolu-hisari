<?php
/** @var array<string, string|null> $settings */
$siteName = SettingsRepository::siteName($settings);
?>
<section class="section hero hero--page">
    <div class="container">
        <span class="eyebrow">Hakkımızda</span>
        <h1><?php echo htmlspecialchars($siteName); ?></h1>
        <p class="hero__lede">Göksu, Beykoz'da özenle seçilmiş çiçeklerle her anı özel kılmayı amaçlayan bir çiçekçiyiz.</p>
    </div>
</section>

<section class="section split">
    <div class="container split__inner">
        <div class="split__text">
            <h2>Çiçekçilik Anlayışımız</h2>
            <p>Her buket, her aranjman bizim için ayrı bir özenle ele alınır. Taze ve kaliteli çiçekleri, doğru kompozisyonla bir araya getirerek hem klasik hem de özgün tasarımlar oluşturuyoruz.</p>
            <p>Amacımız, sadece çiçek sunmak değil; alan için de veren için de anlamlı bir deneyim yaratmak.</p>
        </div>
        <div class="about-photo">
            <img src="/assets/img/about/hakkimizda-cicek.jpg" alt="TSİNAN Flowers atölyesinden sanatsal bir çiçek kompozisyonu" loading="lazy">
        </div>
    </div>
</section>

<section class="section section--tinted">
    <div class="container feature-list">
        <div class="feature-list__item">
            <h3>Taze ve Kaliteli Çiçekler</h3>
            <p>Kullandığımız her çiçek, en iyi görünümü ve ömrü sağlamak için özenle seçilir.</p>
        </div>
        <div class="feature-list__item">
            <h3>Özel Tasarımlar</h3>
            <p>Standart şablonlar yerine, isteğe göre şekillenen kişiselleştirilmiş kompozisyonlar hazırlıyoruz.</p>
        </div>
    </div>
</section>

<section class="section cta-band">
    <div class="container cta-band__inner">
        <h2>Bir Çiçek Seçelim mi?</h2>
        <p>Kataloğumuza göz atın, beğendiğiniz ürün hakkında doğrudan bize ulaşın.</p>
        <a href="/cicekler" class="btn btn--on-dark">Çiçeklerimizi İnceleyin</a>
    </div>
</section>
