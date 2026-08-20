<?php

// Genel uygulama sabitleri.
// İşletmenin gerçek adı, adresi, telefonu vb. netleştiğinde buradan değil,
// admin panel "Site Ayarları" (site_settings tablosu) üzerinden yönetilecek.
// SITE_NAME şimdilik yalnızca <title> ve iskelet görünümü için placeholder'dır.

define('ENVIRONMENT', 'development'); // development | production
define('SITE_NAME', 'Çiçekçi Anadolu Hisarı (Placeholder)');

// Canonical/OG/sitemap URL'leri için merkezi domain ayarı.
// null bırakıldığında Seo::baseUrl() gelen isteğin GERÇEK host'unu kullanır
// (localhost'ta http://localhost, canlıda gerçek domain — asla yanlış/uydurma
// bir domain üretmez). Gerçek domain bağlanıp SABİT bir canonical host
// (ör. www/non-www tercihini kilitlemek için) istenirse aşağıyı doldurun:
// define('SITE_CANONICAL_HOST', 'www.gercekdomain.com.tr');
define('SITE_CANONICAL_HOST', null);

require_once __DIR__ . '/database.php';
