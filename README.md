# Çiçekçi Web Sitesi — Anadolu Hisarı

Anadolu Hisarı (İstanbul) ve çevresine hizmet veren bir çiçekçi için geliştirilmiş, **ürün tanıtım ve katalog** web sitesi. Vanilla PHP + MySQL ile yazılmıştır; framework veya harici bağımlılık kullanmaz.

> **Bu bir e-ticaret sitesi DEĞİLDİR.** Online sipariş, sepet veya ödeme altyapısı içermez. Ziyaretçiler ürünleri ve fiyatlarını inceler, telefon veya WhatsApp üzerinden doğrudan işletmeyle iletişime geçer.

## Özellikler

- **Public site:** ana sayfa, ürün kataloğu, kategori filtreleme, ürün detay sayfası + WhatsApp CTA, galeri, hakkımızda, iletişim
- **Admin panel:** ürün / kategori / galeri yönetimi, site ayarları, güvenli oturum tabanlı giriş
- **Teknik SEO:** dinamik title/description/canonical, Open Graph + Twitter Card, JSON-LD (WebSite, Florist/LocalBusiness, Product, BreadcrumbList), dinamik `sitemap.xml` ve `robots.txt`

## Gereksinimler

- **PHP 8.0 veya üzeri** (önerilen: 8.1+)
- **MySQL 5.7+ / MariaDB 10.3+**
- Gerekli PHP eklentileri:
  - `pdo_mysql` (veritabanı bağlantısı)
  - `fileinfo` (görsel yükleme MIME doğrulaması)
  - `gd` (opsiyonel ama önerilir — görsel optimizasyonu/thumbnail üretimi için; kurulu değilse sistem otomatik olarak orijinal görseli kullanır, hiçbir şeyi kırmaz)

## Kurulum

1. **Web root / docroot'u `public_html/` olarak ayarlayın.** `config/`, `database/` ve `storage/` klasörleri kasıtlı olarak web kökü DIŞINDADIR — tarayıcıdan doğrudan erişilemezler (güvenlik).
2. **Veritabanı bağlantısı:** `config/database.php` dosyasını gerçek veritabanı bilgilerinizle güncelleyin (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`). Depoda bu dosya placeholder değerlerle bulunur.
3. **Veritabanı şeması:** Boş bir MySQL veritabanı oluşturup `database/schema.sql` dosyasını içe aktarın.
4. **(Opsiyonel) Demo veri:** Test amaçlı örnek kategori/ürünler için `database/seed_demo.sql`'i de içe aktarabilirsiniz — tüm ürünler `[DEMO]` etiketlidir ve admin panelden kolayca silinebilir.
5. **İlk admin hesabı:** `public_html/admin/setup.php` adresini tarayıcıda açıp bir kullanıcı adı/şifre belirleyin.
6. **ÖNEMLİ — `setup.php`'yi silin:** Hesap oluşturulduktan sonra bu betik veritabanında zaten bir admin hesabı bulunduğu için kendini otomatik kilitler; yine de güvenlik için sunucudan tamamen silinmesi kesinlikle önerilir.
7. **Gerçek işletme bilgileri:** Admin panel → Site Ayarları ekranından işletme adı, telefon, WhatsApp, e-posta, adres, çalışma saatleri ve sosyal medya bağlantıları girilir. Bu bilgiler koda gömülü değildir, veritabanında (`site_settings`) tutulur.
8. **Domain bağlandığında:** `config/config.php` içindeki `SITE_CANONICAL_HOST` sabitini gerçek domaininizle doldurun. Boş (`null`) bırakıldığında sistem canonical/Open Graph/sitemap URL'lerini isteğin geldiği host'tan otomatik ve doğru şekilde üretir; www/non-www gibi belirli bir tercihi kalıcı olarak sabitlemek isterseniz bu değeri doldurmanız gerekir.

## Proje Yapısı

```
config/           → veritabanı bağlantısı ve genel ayarlar (web kökü DIŞINDA)
database/         → schema.sql, seed_demo.sql
storage/          → login rate-limit verisi (web kökü DIŞINDA, çalışma zamanında oluşur)
public_html/      → WEB ROOT — tüm public + admin kod burada
  ├─ admin/       → yönetim paneli
  ├─ app/
  │   ├─ core/        → veritabanı, auth, CSRF, SEO, repository sınıfları
  │   ├─ controllers/ → sayfa denetleyicileri
  │   └─ views/        → HTML şablonları
  ├─ assets/      → CSS/JS
  └─ uploads/     → admin panelden yüklenen görseller (Git'e dahil değildir)
```

## Deployment Notu

Railway veya benzeri bir PHP+MySQL destekli servise deploy edilirken:

- Hedef sunucuda PHP 8.x + MySQL erişimi sağlanmalı, yukarıdaki eklentiler kurulu olmalı.
- `config/database.php` sunucu ortamındaki **gerçek** veritabanı bilgileriyle güncellenmeli. Bu dosya depoda hâlâ placeholder değerlerle bulunur; gerçek üretim bilgileri girildikten sonra bu dosyayı Git'e commit'lememeye özellikle dikkat edin (bkz. aşağıdaki güvenlik notu).
- Web sunucusunun docroot'u `public_html/` klasörünü göstermeli.

## Güvenlik Notu

Bu depo, `config/database.php` içinde yalnızca placeholder değerler barındırır — gerçek bir kimlik bilgisi içermez. Ancak yerel geliştirme veya canlı sunucuda bu dosyayı gerçek bilgilerle doldurduğunuzda, dosyanın bir daha yanlışlıkla commit'lenmemesi için `git update-index --assume-unchanged config/database.php` kullanmayı veya dosyayı `.gitignore`'a ekleyip yerine bir `config/database.php` şablonunu elle sunucuda oluşturmayı değerlendirin.
