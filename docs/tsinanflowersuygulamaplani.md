# TSİNAN Flowers — Demo'dan Gerçek İşletmeye Geçiş: Uygulama Planı

**Hazırlanma tarihi:** 22 Ağustos 2026
**Girdi:** `tsinan-flowers-veri-paketi.md` (Google Haritalar + Instagram araştırmasıyla doğrulanmış veri paketi)
**Bu belgenin amacı:** VS Code içindeki Claude'un doğrudan uygulayabileceği, kod yazmadan önce onaylanacak, eksiksiz bir dönüşüm planı sunmak.

> **ÖNEMLİ ŞEFFAFLIK NOTU:** Bu oturumda (tarayıcı yan panelinde çalışan araştırma oturumu) projenin gerçek kod dosyalarına (`index.php`, `schema.sql`, admin panel dosyaları vb.) **erişimim yok** — bu dosyalar sizin VS Code ortamınızda duruyor. Bu nedenle Bölüm 1'i, sizin verdiğiniz proje yapısı tanımına (dosya adları, `Auth::attempt()`, `admin_users` tablosu, `site_settings` vb.) ve tipik PHP demo-site kalıplarına dayanarak, **VS Code Claude'un çalıştırması gereken kesin arama komutlarıyla** hazırladım. Aşağıdaki her madde "şurada bu değer olacak" değil, "şurayı şu komutla ara, bulduğunu şu doğrulanmış değerle değiştir" mantığıyla yazılmıştır. Hiçbir dosya içeriği tahmin edilip gerçekmiş gibi sunulmamıştır.

---

## 1. MEVCUT PROJEDE TESPİT EDİLMESİ GEREKEN DEMO/PLACEHOLDER ALANLARI

VS Code Claude, kod değiştirmeden önce aşağıdaki taramaları çalıştırıp **tam bir envanter çıkarmalı**. Her biri için önerilen arama deseni verilmiştir (proje PHP olduğu için `grep -rn` veya IDE arama kullanılabilir):

| # | Aranacak Desen | Muhtemel Konum | Ne Yapılacak |
|---|---|---|---|
| 1.1 | `DEMO`, `[DEMO]`, `Placeholder`, `placeholder` (büyük/küçük harf duyarsız) | Tüm proje (`.php`, `.sql`, `.html`, config dosyaları) | Her eşleşmeyi listele, kaynağını (dosya:satır) not et |
| 1.2 | `Anadolu Hisarı (Placeholder)` veya benzeri sahte marka/konum ifadeleri | header, footer, hero, SEO meta | Bölüm 2'deki gerçek verilerle değiştir |
| 1.3 | Sahte telefon/adres deseni (örn. `0555`, `0000`, `Örnek Mah.`, `XXXX`) | iletişim sayfası, footer, `site_settings` seed | Gerçek verilerle değiştir |
| 1.4 | `SITE_NAME`, `APP_NAME`, config sabitleri | `config.php` / `.env` / `bootstrap` dosyası | "TSİNAN Flowers" ile değiştir |
| 1.5 | Demo ürün adları (`Kırmızı Gül Buketi`, `Beyaz Gül Demeti` vb. — Bölüm 3'teki tam liste) | `seed.sql` / `demo_products.sql` / migration dosyaları | Bölüm 5'teki dönüşüm planına göre işlem yap |
| 1.6 | Demo kategori adları | aynı seed dosyaları | Bölüm 5'e göre işlem yap |
| 1.7 | Placeholder SEO (`<title>`, `meta description`, `og:title` vb.) | her sayfanın `<head>` bloğu veya ortak layout/partial | Bölüm 9'daki değerlerle değiştir |
| 1.8 | Placeholder görsel URL'leri (örn. `via.placeholder.com`, `lorem picsum`, `/img/demo/...`) | ürün kartları, galeri, hero | Bölüm 6'daki upload sistemiyle değiştir; gerçek görsel yoksa fallback göster (uydurma URL kullanılmaz) |
| 1.9 | `admin_users` tablosunda seed admin olup olmadığı | `schema.sql`, `seed.sql` | Yoksa Bölüm 4'teki güvenli oluşturma planını uygula |
| 1.10 | Statik/sabit metinlerde "20 yıllık", "en iyi", "aynı gün teslimat", "lider" gibi ifadeler | Hakkımızda, hero, footer | Tamamı kaldırılacak (Bölüm 2 kuralları) |

**Çıktı beklentisi:** VS Code Claude, bu taramanın sonucunda dosya:satır bazında bir "DEMO ENVANTERİ" listesi çıkarmalı ve **hiçbir maddeyi atlamadan** Bölüm 2–9'daki karşılıklarla eşleştirmelidir.

---

## 2. GERÇEK VERİLERLE DEĞİŞTİRİLECEK ALANLAR — TEK DOĞRULUK KAYNAĞI (SOURCE OF TRUTH)

Aşağıdaki tablo, projede geçen her placeholder alanın **kesin karşılığıdır**. Veri paketindeki doğrulama durumları korunmuştur.

| Alan | Kullanılacak Değer | Durum |
|---|---|---|
| İşletme adı | TSİNAN Flowers | Doğrulandı |
| Kategori | Çiçekçi | Doğrulandı |
| Adres | Göksu, Sadri Alışık Cd. No:3, 34815 Beykoz/İstanbul | Doğrulandı |
| Telefon / WhatsApp | 0532 626 28 89 | Doğrulandı |
| İkinci telefon (0216 308 5859) | **KULLANILMAYACAK** | Doğrulanamadı — siteye eklenmeyecek |
| Instagram | @tsinanflowers | Doğrulandı |
| Telegram | t.me/tsinanflowers | Doğrulandı |
| Çalışma saatleri | Pzt–Cmt 09:00–19:00, Pazar 09:00–17:00 | Doğrulandı |
| Google Maps koordinat | 41.079039, 29.0738468 | Doğrulandı |
| Hizmet seçenekleri | Mağaza içinde alışveriş, Mağazadan teslim alma, Adrese servis | Doğrulandı |
| Web sitesi (resmi) | Yok — kayıtlı değil | Doğrulandı (yokluğu doğrulandı) |
| "X yıllık tecrübe" | **ÜRETİLMEYECEK** | Doğrulanamadı |
| "En iyi / lider / İstanbul'un tercihi" | **ÜRETİLMEYECEK** | Doğrulanamadı |
| "Aynı gün teslimat" vaadi | **ÜRETİLMEYECEK** | Doğrulanamadı |
| Ürün fiyatları | **ÜRETİLMEYECEK** — fiyat yoksa "Fiyat için iletişime geçin" | Doğrulanamadı/eksik |
| Düğün & Organizasyon kategorisi | Ana kategori olarak **eklenmeyecek** (bkz. Bölüm 5) | Doğrulanamadı |
| Özel Günler kategorisi | Ayrı kategori olarak **eklenmeyecek** | Doğrulanamadı |

**Kural:** Kod içinde bu tablodaki değerlerden herhangi biriyle çelişen bir sabit değer bulunursa, **bu tablo geçerlidir**, dosyadaki eski değer değil.

---

## 3. VERİTABANI DEĞİŞİKLİK PLANI

### 3.1 Genel Yaklaşım
- Mevcut `schema.sql` **doğrudan üzerine yazılmayacak**. Değişiklikler idempotent (tekrar çalıştırılabilir) migration mantığıyla uygulanacak.
- Her `CREATE TABLE` için `IF NOT EXISTS` kullanılacak.
- Kolon eklemeleri için önce `INFORMATION_SCHEMA.COLUMNS` üzerinden varlık kontrolü yapılacak (MySQL sürümü `ADD COLUMN IF NOT EXISTS` desteklemiyorsa — sürüm VS Code Claude tarafından `SELECT VERSION();` ile teyit edilmeli).
- Demo veri temizliği **transaction içinde**, geri alınabilir şekilde yapılacak (`START TRANSACTION` / `COMMIT`), silme öncesi mevcut veri bir `_backup` tablosuna veya dosyaya yedeklenecek.

### 3.2 Beklenen/Önerilen Tablo Yapısı (mevcut schema ile karşılaştırılıp uyarlanacak)

```sql
-- site_settings: tek satırlık veya key-value yapı (mevcut yapıya göre uyarlanacak)
CREATE TABLE IF NOT EXISTS site_settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  business_name VARCHAR(255) NOT NULL DEFAULT 'TSİNAN Flowers',
  phone VARCHAR(32) NOT NULL DEFAULT '0532 626 28 89',
  whatsapp VARCHAR(32) NOT NULL DEFAULT '905326262889',
  telegram_url VARCHAR(255) NOT NULL DEFAULT 'https://t.me/tsinanflowers',
  instagram_url VARCHAR(255) NOT NULL DEFAULT 'https://www.instagram.com/tsinanflowers/',
  address VARCHAR(500) NOT NULL DEFAULT 'Göksu, Sadri Alışık Cd. No:3, 34815 Beykoz/İstanbul',
  map_lat DECIMAL(10,7) DEFAULT 41.0790390,
  map_lng DECIMAL(10,7) DEFAULT 29.0738468,
  working_hours_json JSON NULL, -- {"mon":"09:00-19:00", ..., "sun":"09:00-17:00"}
  logo_path VARCHAR(255) NULL,
  seo_default_title VARCHAR(255) DEFAULT 'TSİNAN Flowers | Beykoz Göksu Çiçekçi',
  seo_default_description VARCHAR(500) DEFAULT 'Beykoz Göksu''da butik çiçek stüdyosu TSİNAN Flowers.',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  category_id INT NOT NULL,
  name VARCHAR(180) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  description TEXT NULL,
  price DECIMAL(10,2) NULL,               -- NULL = fiyat yok
  price_status ENUM('confirmed','contact','hidden') NOT NULL DEFAULT 'contact',
  main_image VARCHAR(255) NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  meta_title VARCHAR(255) NULL,
  meta_description VARCHAR(500) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS product_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS gallery (
  id INT PRIMARY KEY AUTO_INCREMENT,
  image_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- admin_users: mevcut tabloya güvenlik/throttle kolonları eklenecek (varsa dokunulmayacak)
-- Beklenen minimum kolonlar: id, username (UNIQUE), password_hash, created_at
-- Eklenecek (yoksa): failed_attempts INT DEFAULT 0, locked_until DATETIME NULL, last_login_at DATETIME NULL
```

> **Not:** Mevcut `schema.sql` bu yapıdan farklıysa (örn. farklı kolon adları, `admin_users` içinde zaten `password` adında bir kolon varsa), VS Code Claude mevcut şemayı **esas alacak**, yukarıdaki yapı sadece referans/kontrol listesidir. Mevcut yapıyla çelişen hiçbir kolon zorla eklenmeyecek.

### 3.3 Demo Veri Temizliği (idempotent)

```sql
START TRANSACTION;

-- Güvenlik: silmeden önce yedekle
CREATE TABLE IF NOT EXISTS products_backup_predeploy AS SELECT * FROM products WHERE 1=0;
INSERT INTO products_backup_predeploy SELECT * FROM products WHERE name LIKE '[DEMO]%';

DELETE FROM products WHERE name LIKE '[DEMO]%';
-- Kategori temizliği yalnızca ilişkili ürün kalmadıysa yapılmalı (bkz. Bölüm 5.3)

COMMIT;
```

### 3.4 Migration Sırası
1. `site_settings` gerçek verilerle güncelle/seed et (Bölüm 3.2 default değerleri veya `UPDATE` ile).
2. Yeni kategori/ürün/galeri tablolarını oluştur (yoksa).
3. Demo ürün/kategori verilerini temizle (Bölüm 3.3).
4. Gerçek kategorileri ekle (Bölüm 5.1).
5. Gerçek ürün taslaklarını `price_status='contact'` ile ekle (Bölüm 5.2).
6. Admin hesabını oluştur (Bölüm 4).

---

## 4. ADMIN HESABININ GÜVENLİ OLUŞTURULMA PLANI

### 4.1 Kesin Kural
Şifre **hiçbir zaman** düz metin olarak SQL dosyasına, `.env` dosyasına, log'a veya veritabanına yazılmayacak. Yalnızca `password_hash()` çıktısı saklanacak.

### 4.2 Önerilen Yöntem — Tek Seferlik PHP Seeder Script (birincil öneri)

`scripts/seed_admin.php` (veya mevcut proje yapısına uygun bir konumda) oluşturulacak:

```php
<?php
// scripts/seed_admin.php
// Kullanım: php scripts/seed_admin.php
// Şifre ortam değişkeninden okunur, koda veya SQL'e YAZILMAZ.

require __DIR__ . '/../config.php'; // mevcut DB bağlantı dosyası neyse ona uyarlanacak

$username = 'tsinanadmin';
$plainPassword = getenv('ADMIN_SEED_PASSWORD');

if (!$plainPassword) {
    fwrite(STDERR, "HATA: ADMIN_SEED_PASSWORD ortam değişkeni tanımlı değil.\n");
    exit(1);
}

$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO admin_users (username, password_hash, created_at)
     VALUES (:u, :h, NOW())
     ON DUPLICATE KEY UPDATE password_hash = :h2"
);
$stmt->execute([':u' => $username, ':h' => $hash, ':h2' => $hash]);

echo "Admin hesabı '$username' güvenli şekilde oluşturuldu/güncellendi.\n";
```

Çalıştırma (VS Code terminalinde, tek seferlik):
```bash
ADMIN_SEED_PASSWORD='Tsinan2026*-' php scripts/seed_admin.php
```

Çalıştırdıktan sonra:
- Terminal geçmişinde şifrenin kalmaması için `history -d` (bash) veya terminali kapatma önerilir.
- `scripts/seed_admin.php` dosyası **plaintext şifre içermez**, repoda kalabilir (gelecekte başka admin eklemek için tekrar kullanılabilir).
- `ON DUPLICATE KEY UPDATE` sayesinde script tekrar çalıştırılırsa hata vermez, mevcut hash'i günceller (idempotent) — bu, `admin_users.username` üzerinde `UNIQUE` kısıtı olmasını gerektirir; yoksa eklenmeli.

### 4.3 Alternatif Yöntem — Doğrudan SQL (yalnızca script çalıştırılamıyorsa)

1. Terminalde hash üretilir (proje ile aynı PHP sürümünde çalıştırılmalı):
   ```bash
   php -r "echo password_hash('Tsinan2026*-', PASSWORD_DEFAULT) . PHP_EOL;"
   ```
2. Çıktı (örn. `$2y$10$...`) aşağıdaki gibi bir seed SQL'e **yalnızca hash olarak** yapıştırılır:
   ```sql
   INSERT INTO admin_users (username, password_hash, created_at)
   SELECT 'tsinanadmin', '<BURAYA_HASH_YAPISTIRILACAK>', NOW()
   WHERE NOT EXISTS (SELECT 1 FROM admin_users WHERE username = 'tsinanadmin');
   ```
3. Bu SQL dosyası versiyon kontrolüne eklenmeden önce hash'in **düz metin şifre olmadığı** teyit edilir (bcrypt hash'i `$2y$` ile başlar).

### 4.4 `Auth::attempt()` Uyumluluğu
- Mevcut `Auth::attempt()` fonksiyonu incelenip `password_verify($inputPassword, $storedHash)` kullandığından emin olunmalı.
- Eğer mevcut kod `password_verify` yerine düz metin karşılaştırma (`===`) yapıyorsa, bu **kritik bir güvenlik açığıdır** ve mutlaka `password_verify()` ile değiştirilmelidir — bu değişiklik, veri paketi kapsamı dışında olsa da güvenlik kuralı gereği zorunludur.
- `password_needs_rehash()` ile gelecekte algoritma yükseltmesi (örn. bcrypt → argon2id) destekleniyorsa login sırasında otomatik rehash uygulanabilir (opsiyonel iyileştirme).

### 4.5 Kurulum Sonrası Güvenlik Notu
Şifre bu konuşma metninde düz olarak paylaşıldı. Kurulum tamamlandıktan sonra **admin panelden şifreyi değiştirmeniz** önerilir; bu, herhangi bir kayıt/log sızıntısı riskine karşı iyi bir güvenlik pratiğidir.

---

## 5. ÜRÜN / KATEGORİ DÖNÜŞÜM PLANI

### 5.1 Kategoriler

| Kategori | İşlem | Gerekçe |
|---|---|---|
| Buketler | Ekle, aktif | Doğrulandı |
| Güller | Ekle, aktif | Doğrulandı |
| Orkideler | Ekle, aktif | Doğrulandı |
| Saksı Bitkileri | Ekle, aktif | Doğrulandı (sınırlı örnek, izlenmeli) |
| Özel Tasarımlar / Sanatsal Kompozisyonlar | Ekle, aktif | Doğrulandı |
| **Tören & Kutlama Çiçekleri** | Ekle ama **`is_active = 0`** (pasif/taslak) | Görsel var ama tür (düğün/açılış/anma) teyit edilmedi — **en güvenli çözüm: kategoriyi oluştur, ana menüde gösterme, işletme sahibi netleştirince aktif et** |
| Düğün & Organizasyon | **Eklenmeyecek** | Doğrulanamadı |
| Özel Günler | **Eklenmeyecek** | Doğrulanamadı |

### 5.2 Ürünler
Veri paketi Bölüm 5'teki 11 ürün taslağı, aşağıdaki kurallarla veritabanı şemasına uygulanacak:

- Her ürün `price = NULL`, `price_status = 'contact'` olarak eklenecek.
- `is_active = 1` (yayınlanabilir) — **istisna:** "Tören / Kutlama Çiçek Standı" ürünü, kategorisiyle birlikte `is_active = 0` olarak eklenecek (tür teyidi bekleniyor).
- `main_image = NULL` başlangıçta — gerçek görsel admin panelden yüklenene kadar fallback UI gösterilecek (Bölüm 6).
- `is_featured`: veri paketindeki "Öne çıkan mı" sütunundaki "Evet" işaretli 6 ürün için `1`.
- `sort_order`: veri paketindeki sıralamaya göre (1'den başlayarak) atanacak.
- `slug`: veri paketindeki önerilen slug'lar birebir kullanılacak (örn. `kirmizi-gul-demeti`).

### 5.3 Demo → Gerçek Geçiş Sırası (veri kaybını önlemek için)
1. Önce yeni gerçek kategoriler eklenir (demo kategoriler silinmeden).
2. Yeni gerçek ürünler eklenir.
3. Demo ürünler `products_backup_predeploy` tablosuna yedeklendikten sonra silinir (Bölüm 3.3).
4. Demo kategoriler, **ilişkili hiç ürün kalmadığı teyit edildikten sonra** silinir:
   ```sql
   DELETE FROM categories
   WHERE id NOT IN (SELECT DISTINCT category_id FROM products)
     AND name NOT IN ('Buketler','Güller','Orkideler','Saksı Bitkileri','Özel Tasarımlar / Sanatsal Kompozisyonlar','Tören & Kutlama Çiçekleri');
   ```
5. Foreign key kısıtı nedeniyle ilişkili ürünü olan bir demo kategori varsa, önce o ürünler silinmeden kategori silinmemeli (hata almak yerine kontrol edilmeli).

---

## 6. GÖRSEL UPLOAD VE ENTEGRASYON PLANI

Instagram'dan otomatik görsel çekme/indirme **yapılmayacak** (erişim koruması aşılmayacak). Bunun yerine admin panelden manuel yükleme sistemi kurulacak.

### 6.1 Upload Güvenliği
- **Dosya türü kontrolü:** Uzantıya değil, gerçek içerik türüne bakılacak — PHP'de `finfo_file()` ile MIME tespiti (`image/jpeg`, `image/png`, `image/webp` dışındakiler reddedilecek).
- **Boyut kontrolü:** Sunucu tarafında `php.ini` (`upload_max_filesize`, `post_max_size`) ile uyumlu, örn. maksimum 5 MB; aşan dosyalar reddedilip kullanıcıya net hata mesajı gösterilecek.
- **Benzersiz dosya adı:** Orijinal dosya adı kullanılmayacak — `bin2hex(random_bytes(16)) . '.' . $ext` gibi rastgele/öngörülemez isim üretilecek (path traversal ve isim çakışmasını önlemek için).
- **Depolama konumu:** Görseller çalıştırılabilir kod içeremeyen bir dizinde tutulacak (örn. `/public/uploads/products/`), bu dizine `.htaccess` (Apache) ile PHP çalıştırma engeli konacak:
  ```apache
  <FilesMatch "\.(php|phtml|php\d)$">
    Require all denied
  </FilesMatch>
  ```
  (Nginx kullanılıyorsa eşdeğer `location` bloğu ile `.php` dosyalarının bu dizinde çalıştırılması engellenecek.)
- **Görsel yeniden işleme (opsiyonel ama önerilir):** Yüklenen görsel GD/Imagick ile yeniden encode edilerek (örn. `imagecreatefromjpeg` → `imagejpeg`) olası gömülü zararlı payload'lar temizlenir ve EXIF/GPS verisi silinir.
- **Eski görsel silme sırası:** Yeni görsel başarıyla yüklenip veritabanı güncellendikten **sonra** eski dosya silinecek (önce sil–sonra yükle sırası kullanılmayacak, aksi halde hata durumunda görselsiz kalınabilir). Silme öncesi dosya yolunun beklenen upload dizini içinde olduğu doğrulanacak (`realpath()` kontrolü ile path traversal engellenecek).

### 6.2 Fallback / Kırık Görsel Önleme
- `main_image` veya `product_images` boşsa, şablon katmanında sabit bir yerel placeholder (`/assets/img/placeholder-flower.svg` gibi, projeyle birlikte gelen gerçek bir dosya) gösterilecek — **hiçbir zaman dış/uydurma URL değil**.
- Görüntüleme öncesi `file_exists()` kontrolü yapılacak; DB'de kayıt olup dosya fiziksel olarak yoksa da aynı fallback devreye girecek (kırık `<img>` yerine).

### 6.3 Admin Panel Upload Akışı
- Ürün ekle/düzenle formunda: "Ana Görsel" (tekil) + "Ek Görseller" (çoklu, sürükle-bırak veya çoklu seçim) alanları.
- Galeri yönetiminde: toplu görsel yükleme + her biri için alt metin (SEO/erişilebilirlik) girişi.
- Yükleme sırasında önizleme (client-side) gösterilecek, sunucu tarafı doğrulama yine de zorunlu tutulacak (client-side kontrol tek başına yeterli değildir).

---

## 7. PUBLIC SİTE — SAYFA SAYFA DEĞİŞİKLİK PLANI

### 7.1 `index.php` (Ana Sayfa)
- Hero başlık → "Beykoz'da Tasarımla Buluşan Çiçekler"
- Hero alt metin → "TSİNAN Flowers — Göksu'da, her aranjmanı özenle tasarlıyoruz."
- CTA butonları → "Koleksiyonu Keşfet" (çiçeklerimiz sayfasına), "WhatsApp'tan Sipariş Ver" (`https://wa.me/905326262889`), "Bize Ulaşın" (iletişim sayfasına)
- Öne çıkan ürünler bölümü → `is_featured=1` ürünler DB'den çekilecek (demo sabit veri değil, dinamik sorgu)
- SEO `<title>` ve meta description → Bölüm 9'daki değerlerle

### 7.2 Çiçeklerimiz Sayfası
- Kategori filtreleri → Bölüm 5.1'deki 5 aktif kategori (Tören & Kutlama pasif olduğu için filtre listesinde görünmeyecek, aktif edilene kadar)
- Ürün kartları → dinamik DB sorgusu, `is_active=1` ürünler
- Fiyat gösterimi → `price_status='contact'` ise "Fiyat için WhatsApp'tan ulaşın" metni + WhatsApp linki; `price` doluysa formatlanmış TL gösterimi
- SEO `<title>` → "Çiçeklerimiz | TSİNAN Flowers Anadolu Hisarı Çiçekçi"

### 7.3 Galeri Sayfası
- `gallery` tablosundan dinamik çekim, demo statik görsel listesi kaldırılacak
- SEO `<title>` → "Galeri | TSİNAN Flowers Beykoz Çiçekçi"

### 7.4 Hakkımızda Sayfası
- Placeholder metin → Veri paketi Bölüm 8'deki taslak metinle değiştirilecek (kanıtlanmamış iddia içermeyen versiyon)
- SEO `<title>` → "Hakkımızda | TSİNAN Flowers"

### 7.5 İletişim Sayfası
- Adres, telefon, çalışma saatleri → Bölüm 2 tablosundaki kesin değerler
- Google Haritalar embed → koordinat 41.079039, 29.0738468 kullanılarak iframe/embed
- Aksiyon butonları:
  - "Telefonla Ara" → `tel:+905326262889`
  - "WhatsApp" → `https://wa.me/905326262889`
  - "Telegram" → `https://t.me/tsinanflowers`
  - "Yol Tarifi" → Google Maps place linkine yönlendirme
- SEO `<title>` → "İletişim | TSİNAN Flowers Göksu Çiçekçi"

### 7.6 Header / Footer (ortak layout)
- `SITE_NAME` / marka adı → "TSİNAN Flowers"
- Footer iletişim bilgileri → Bölüm 2 tablosu
- Footer sosyal linkler → Instagram (@tsinanflowers), Telegram (t.me/tsinanflowers) — Facebook/Twitter gibi doğrulanmamış platformlar **eklenmeyecek**

### 7.7 Genel Kontroller (tüm sayfalarda)
- `SITE_NAME`, telefon, adres gibi tekrar eden değerler **tek bir yerden** (`site_settings` tablosu veya config) çekilecek, her sayfada ayrı ayrı hardcode edilmeyecek — bu hem tutarlılığı sağlar hem de gelecekte admin panelden güncellemeyi mümkün kılar.
- Mobil görünüm: hero, ürün kartları, WhatsApp/telefon butonlarının dokunma hedefi boyutları (min. 44x44px) kontrol edilecek.

---

## 8. ADMIN PANEL — SAYFA SAYFA DEĞİŞİKLİK PLANI

### 8.1 Login
- Kullanıcı adı/şifre formu → `Auth::attempt()` üzerinden `password_verify()` ile doğrulama (Bölüm 4.4)
- Hatalı girişte **genel** hata mesajı ("Kullanıcı adı veya şifre hatalı") — hangi alanın yanlış olduğu belirtilmeyecek
- Başarılı girişte `session_regenerate_id(true)` çağrılacak (session fixation önleme)
- CSRF token, login formuna da eklenecek

### 8.2 Login Throttle
- `admin_users` tablosuna (veya ayrı bir `login_attempts` tablosuna) `failed_attempts`, `locked_until` kolonları eklenecek
- Örn. 5 başarısız denemeden sonra 15 dakika kilit; kilit süresince "Çok fazla başarısız deneme, lütfen X dakika sonra tekrar deneyin" mesajı
- Başarılı girişte `failed_attempts` sıfırlanacak

### 8.3 Logout / Session
- Logout: `session_destroy()` + oturum çerezinin geçersiz kılınması
- Session çerezi bayrakları: `HttpOnly`, `Secure` (HTTPS ortamında), `SameSite=Strict` veya `Lax`
- Admin sayfalarının tamamı, her istekte oturum geçerliliğini kontrol eden bir middleware/guard'dan geçecek

### 8.4 Dashboard
- Toplam ürün, aktif ürün, pasif ürün, kategori sayısı → canlı DB sorgularıyla (COUNT)
- Son güncellenen ürünler (varsa) → `updated_at DESC LIMIT 5`

### 8.5 Ürün Yönetimi
- Liste: arama/filtreleme (kategori, aktif/pasif, öne çıkan)
- Ekle/Düzenle formu: ad, slug (otomatik öneri + manuel düzenlenebilir), açıklama, kategori (dropdown), fiyat (opsiyonel sayı alanı) + fiyat durumu (confirmed/contact/hidden radio), ana görsel upload, ek görseller upload, öne çıkan (checkbox), aktif/pasif (checkbox), sıralama (sayı), meta title/description (opsiyonel, boşsa `site_settings` default'una düşer)
- Silme: onay modalı + ilişkili görsellerin de silinmesi (cascade, Bölüm 3.2'deki `ON DELETE CASCADE`)

### 8.6 Kategori Yönetimi
- Ekle/Düzenle: ad, slug, aktif/pasif, sıralama
- Silme: **önce ilişkili ürün kontrolü** — ürün varsa silme engellenip "Bu kategoride N ürün var, önce onları taşıyın/silin" uyarısı gösterilecek

### 8.7 Galeri Yönetimi
- Görsel ekle (tekli/çoklu), alt metin, sıralama, aktif/pasif, silme (onay modalı)

### 8.8 Site Ayarları
- Tek form: işletme adı, telefon, WhatsApp, Telegram, Instagram, adres, çalışma saatleri (gün bazlı), logo upload, varsayılan SEO title/description
- Kaydetme sonrası **public sitede** değişikliğin göründüğü doğrulanacak (Bölüm 11 test listesi)

---

## 9. SEO PLANI

| Sayfa | Title | Meta Description |
|---|---|---|
| Ana Sayfa | TSİNAN Flowers \| Beykoz Göksu Çiçekçi | Beykoz Göksu'da butik çiçek stüdyosu TSİNAN Flowers. Güller, orkideler ve özel tasarım aranjmanlar. |
| Çiçeklerimiz | Çiçeklerimiz \| TSİNAN Flowers Anadolu Hisarı Çiçekçi | Buketlerden orkidelere, özel tasarım kompozisyonlardan saksı bitkilerine TSİNAN Flowers koleksiyonunu keşfedin. |
| Galeri | Galeri \| TSİNAN Flowers Beykoz Çiçekçi | TSİNAN Flowers stüdyosundan gerçek tasarımlar ve aranjmanlar. |
| Hakkımızda | Hakkımızda \| TSİNAN Flowers | Beykoz Göksu'da tasarım odaklı bir çiçek stüdyosu: TSİNAN Flowers'ı tanıyın. |
| İletişim | İletişim \| TSİNAN Flowers Göksu Çiçekçi | TSİNAN Flowers'a Beykoz Göksu'daki stüdyomuzdan, telefon, WhatsApp veya Telegram üzerinden ulaşın. |

- Anahtar kelimeler doğal biçimde: "Tsinan Flowers", "Anadolu Hisarı çiçekçi", "Beykoz çiçekçi", "Göksu çiçekçi" — spam yapılmayacak.
- Her sayfada tek bir `<h1>`, açıklayıcı `alt` metinleri (özellikle ürün ve galeri görselleri için), `og:title`/`og:description`/`og:image` temel Open Graph etiketleri eklenmesi önerilir.
- `robots.txt` ve varsa `sitemap.xml` demo domain/placeholder URL içermediğinden emin olunmalı.

---

## 10. GÜVENLİK KONTROLLERİ (Checklist)

- [ ] Şifreler yalnızca `password_hash()` ile saklanıyor, `password_verify()` ile doğrulanıyor
- [ ] Hiçbir SQL dosyasında/`.env`'de/log'da düz metin şifre yok
- [ ] Login formu CSRF token içeriyor ve sunucu tarafında doğrulanıyor
- [ ] Tüm admin state-changing formları (ürün/kategori/galeri/ayarlar) CSRF korumalı
- [ ] Login throttle/rate-limit aktif (Bölüm 8.2)
- [ ] Hatalı giriş mesajları genel (kullanıcı adı/şifre ayrımı yapılmıyor)
- [ ] Başarılı girişte session ID yenileniyor (`session_regenerate_id`)
- [ ] Session çerezleri `HttpOnly`/`Secure`/`SameSite` bayraklı
- [ ] Tüm admin sayfaları oturum kontrolünden geçiyor (doğrudan URL ile erişim engelleniyor)
- [ ] Upload edilen dosyalar MIME/tür/boyut kontrolünden geçiyor
- [ ] Upload dizininde script çalıştırma engellenmiş (`.htaccess` veya sunucu config)
- [ ] Dosya adları benzersiz/öngörülemez üretiliyor
- [ ] Dosya silme işlemleri path traversal'a karşı korunuyor (`realpath` kontrolü)
- [ ] Tüm DB sorguları prepared statement / parametreli sorgu kullanıyor (SQL injection koruması)
- [ ] Kullanıcı girdileri çıktıya yazılırken HTML-escape ediliyor (XSS koruması, özellikle ürün açıklamaları ve admin panel form alanları)
- [ ] Hata mesajlarında sunucu/veritabanı detayları (stack trace, SQL hatası) son kullanıcıya gösterilmiyor

---

## 11. TEST LİSTESİ

### Public Site
- [ ] Ana sayfa yükleniyor, hero/CTA/öne çıkan ürünler doğru görünüyor
- [ ] Çiçeklerimiz sayfası ve kategori filtreleri çalışıyor (Tören & Kutlama kategorisi görünmüyor — pasif)
- [ ] Ürün detay görünümü varsa doğru veriyi gösteriyor
- [ ] Galeri sayfası dinamik görselleri listeliyor
- [ ] Hakkımızda sayfasında placeholder metin kalmamış
- [ ] İletişim sayfasında doğru adres/telefon/saatler görünüyor
- [ ] Telefon linki (`tel:`) doğru numarayı arıyor
- [ ] WhatsApp linki doğru numarayla açılıyor ve önceden doldurulmuş mesaj (opsiyonel) doğru
- [ ] Telegram linki `t.me/tsinanflowers`'a gidiyor
- [ ] Google Maps embed/yol tarifi doğru konumu gösteriyor
- [ ] Mobil görünümde tüm sayfalar düzgün render oluyor
- [ ] Her sayfanın SEO title/description'ı Bölüm 9'daki değerlerle eşleşiyor
- [ ] Görseli olmayan ürün/galeri kalemlerinde kırık görsel yok, fallback görünüyor
- [ ] Site genelinde "DEMO", "Placeholder" gibi kalıntı metin aramasıyla (Bölüm 1) sıfır sonuç dönüyor

### Admin Panel
- [ ] `tsinanadmin` ile doğru şifreyle giriş başarılı
- [ ] Yanlış şifreyle giriş reddediliyor, genel hata mesajı gösteriyor
- [ ] 5 hatalı denemeden sonra throttle/kilit devreye giriyor
- [ ] Logout sonrası admin sayfalarına doğrudan URL ile erişim login'e yönlendiriyor
- [ ] Dashboard sayıları gerçek DB verileriyle eşleşiyor
- [ ] Ürün ekleme, düzenleme, silme çalışıyor; silinen ürünün görselleri de temizleniyor
- [ ] Görsel yükleme: geçerli dosya kabul ediliyor, geçersiz tür/boyut reddediliyor
- [ ] Kategori silme, ilişkili ürün varken engelleniyor
- [ ] Galeri ekleme/düzenleme/sıralama/silme çalışıyor
- [ ] Site ayarları formundan yapılan değişiklik (örn. telefon numarası) **public sitede** anında/yayında görünüyor
- [ ] CSRF token olmadan gönderilen bir form isteği reddediliyor (manuel test veya otomatik test ile)

---

## VS CODE CLAUDE İÇİN TEK PARÇA UYGULAMA TALİMATI (MASTER PROMPT)

Aşağıdaki blok, olduğu gibi VS Code'daki Claude'a verilebilir:

```
GÖREV: TSİNAN Flowers projesini demo/placeholder aşamasından çıkarıp gerçek,
teslim edilebilir bir çiçekçi web sitesi + admin paneline dönüştür.

ADIM 0 — ANALİZ (kod yazmadan önce):
1. Proje kök dizinini tara: index.php, çiçeklerimiz/galeri/hakkımızda/iletişim
   sayfaları, header/footer partial'ları, admin panel dizini, config/site_settings
   dosyası, schema.sql ve varsa seed.sql dosyalarını listele.
2. Şu desenleri proje genelinde ara ve dosya:satır bazında bir envanter çıkar:
   "DEMO", "[DEMO]", "Placeholder", "placeholder", ve tahmini/sahte telefon-adres
   kalıpları (örn. 0555, Örnek Mah., XXXX).
3. admin_users tablosunun mevcut kolon yapısını (schema.sql üzerinden) çıkar ve
   Auth::attempt() fonksiyonunun password_verify() kullanıp kullanmadığını
   doğrula. Kullanmıyorsa bunu güvenlik açığı olarak işaretle ve düzelt.
4. Bu analiz sonuçlarını bana özetle, SONRA uygulamaya geç.

ADIM 1 — TEK DOĞRULUK KAYNAĞI (bu bilgiler dışında hiçbir işletme bilgisi
üretme, tahmin etme veya "muhtemelen" diye ekleme):
- İşletme adı: TSİNAN Flowers
- Kategori: Çiçekçi
- Adres: Göksu, Sadri Alışık Cd. No:3, 34815 Beykoz/İstanbul
- Telefon/WhatsApp: 0532 626 28 89 (wa.me formatı: 905326262889)
- Instagram: @tsinanflowers (https://www.instagram.com/tsinanflowers/)
- Telegram: t.me/tsinanflowers
- Çalışma saatleri: Pzt-Cmt 09:00-19:00, Pazar 09:00-17:00
- Harita koordinatı: 41.079039, 29.0738468
- Hizmetler: Mağaza içinde alışveriş, Mağazadan teslim alma, Adrese servis
- KULLANILMAYACAK: 0216 308 5859 numarası, "X yıllık tecrübe", "en iyi/lider"
  iddiaları, "aynı gün teslimat" vaadi, tahmini fiyatlar, zorunlu "Düğün &
  Organizasyon" veya "Özel Günler" kategorileri.

ADIM 2 — VERİTABANI:
- site_settings, categories, products, product_images, gallery tablolarını
  (yoksa) idempotent CREATE TABLE IF NOT EXISTS ile oluştur; mevcut şemayla
  çakışma varsa mevcut şemayı esas al.
- Demo ürün/kategori verilerini silmeden önce bir backup tabloya kopyala,
  sonra sil (transaction içinde).
- Gerçek kategorileri ekle: Buketler, Güller, Orkideler, Saksı Bitkileri,
  Özel Tasarımlar/Sanatsal Kompozisyonlar (hepsi aktif) ve Tören & Kutlama
  Çiçekleri (is_active=0, tür teyidi bekleniyor).
- Ekteki veri paketindeki 11 ürün taslağını price=NULL, price_status='contact'
  ile ekle; "Tören/Kutlama Çiçek Standı" ürününü is_active=0 olarak ekle.

ADIM 3 — ADMIN HESABI:
- scripts/seed_admin.php dosyası oluştur: ADMIN_SEED_PASSWORD ortam
  değişkeninden şifreyi okusun, password_hash(PASSWORD_DEFAULT) ile hashlesin,
  admin_users tablosuna username='tsinanadmin' ile INSERT ... ON DUPLICATE
  KEY UPDATE yapsın. Şifreyi hiçbir dosyaya düz metin yazma.
- Bu scripti "ADMIN_SEED_PASSWORD='Tsinan2026*-' php scripts/seed_admin.php"
  komutuyla çalıştır (terminalde, kalıcı olarak hiçbir yere kaydetmeden).
- Auth::attempt() fonksiyonunun password_verify() ile çalıştığını doğrula/düzelt.

ADIM 4 — GÖRSEL SİSTEMİ:
- Admin panelde ürün/galeri görsel upload akışını kur: MIME kontrolü (finfo),
  boyut sınırı, benzersiz dosya adı (random_bytes), upload dizininde script
  çalıştırma engeli (.htaccess veya sunucu config), eski görseli yeni
  yükleme başarılı olduktan sonra silme, path traversal koruması.
- Görseli olmayan ürün/galeri kalemleri için projeyle birlikte gelen gerçek
  bir yerel placeholder görsel/SVG göster — asla uydurma dış URL kullanma.
- Instagram'dan otomatik görsel indirme yapma; görseller admin panelden
  manuel yüklenecek.

ADIM 5 — PUBLIC SİTE SAYFALARI:
- index.php, çiçeklerimiz, galeri, hakkımızda, iletişim, header, footer
  dosyalarındaki tüm DEMO/placeholder içerikleri Adım 1'deki bilgilerle
  (ve ekli veri paketindeki hero/hakkımızda/iletişim metin taslaklarıyla)
  değiştir. Tekrar eden bilgileri (telefon, adres vb.) tek bir yerden
  (site_settings) çek, sayfa sayfa hardcode etme.
- WhatsApp/telefon/Telegram/Google Maps yol tarifi linklerini doğru
  değerlerle bağla.

ADIM 6 — ADMIN PANEL:
- Login: password_verify, genel hata mesajı, session_regenerate_id,
  CSRF token, login throttle (5 hatalı denemeden sonra geçici kilit).
- Dashboard: toplam/aktif/pasif ürün ve kategori sayıları canlı sorgu ile.
- Ürün/Kategori/Galeri/Site Ayarları CRUD ekranlarını ekteki plana göre kur
  (fiyat durumu desteği, kategori silmede ilişkili ürün kontrolü dahil).

ADIM 7 — SEO:
- Her sayfanın title/meta description'ını ekteki tabloya göre güncelle.
  Anahtar kelime doldurma yapma.

ADIM 8 — GÜVENLİK VE TEST:
- Ekteki güvenlik checklist'inin (CSRF, session, upload, SQL injection, XSS,
  hata mesajı sızıntısı) tamamını uygula ve doğrula.
- Ekteki test listesindeki tüm public site ve admin panel maddelerini çalıştır,
  sonuçları raporla.
- Proje genelinde "DEMO"/"Placeholder" araması yaparak sıfır sonuç döndüğünü
  doğrula.

KISITLAR (tekrar):
- Hiçbir tahmini fiyat, sahte iddia veya doğrulanmamış bilgi ekleme.
- Hiçbir görsel URL'si uydurma; görsel yoksa yerel fallback kullan.
- Şifreyi hiçbir yerde düz metin olarak saklama.
- Değişiklikleri uygulamadan önce Adım 0'daki analiz sonucunu özetle.
```

---

*Bu belge, `tsinan-flowers-veri-paketi.md` içindeki doğrulanmış verilere dayanır. Gerçek proje dosyalarına bu oturumdan erişilemediği için Bölüm 1 ve 3, VS Code Claude'un kendi ortamında çalıştırıp doğrulaması gereken arama/kontrol adımları olarak yazılmıştır.*
