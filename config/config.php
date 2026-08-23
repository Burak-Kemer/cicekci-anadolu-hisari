<?php

// Genel uygulama sabitleri.
// İşletme bilgileri (adres, telefon, sosyal medya vb.) buradan değil,
// admin panel "Site Ayarları" (site_settings tablosu) üzerinden yönetilir.
// SITE_NAME, site_settings.site_name doldurulana kadar (ör. ilk kurulumda
// veya DB bağlantısı henüz yokken) kullanılan gerçek işletme adıdır —
// artık bir placeholder değildir.

define('ENVIRONMENT', 'development'); // development | production
define('SITE_NAME', 'TSİNAN Flowers');

// Canonical/OG/sitemap URL'leri için merkezi domain ayarı.
// null bırakıldığında Seo::baseUrl() gelen isteğin GERÇEK host'unu kullanır
// (localhost'ta http://localhost, canlıda gerçek domain — asla yanlış/uydurma
// bir domain üretmez). Gerçek domain bağlanıp SABİT bir canonical host
// (ör. www/non-www tercihini kilitlemek için) istenirse aşağıyı doldurun:
// define('SITE_CANONICAL_HOST', 'www.gercekdomain.com.tr');
define('SITE_CANONICAL_HOST', null);

require_once __DIR__ . '/database.php';
