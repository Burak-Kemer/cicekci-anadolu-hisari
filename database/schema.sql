-- ============================================================
-- TSİNAN Flowers — Veritabanı Şeması
-- ============================================================
-- Bu dosyayı, hosting kontrol panelinizde (cPanel > MySQL Veritabanları)
-- önceden oluşturduğunuz BOŞ veritabanına phpMyAdmin > İçe Aktar (Import)
-- sekmesinden yükleyin. CREATE DATABASE / USE ifadesi kasıtlı olarak
-- içermez; veritabanı adı hosting sağlayıcısına göre değişir.
--
-- Bu proje bir E-TİCARET sistemi DEĞİLDİR. Sipariş / sepet / ödeme /
-- müşteri hesabı tablosu barındırmaz — sadece katalog + admin panel
-- altyapısı için gerekli tablolar tanımlanmıştır.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(150) NOT NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_categories_slug (slug),
    KEY idx_categories_active_sort (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ------------------------------------------------------------
-- products
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(150) NOT NULL,
    slug                VARCHAR(180) NOT NULL,
    description         TEXT NULL,
    -- price NULL + price_status='contact' = "fiyat için iletişime geçin".
    -- Tahmini/uydurma fiyat asla üretilmez; fiyat netleşmeden ürün bu
    -- durumda yayınlanabilir.
    price               DECIMAL(10,2) UNSIGNED NULL,
    price_status        ENUM('fixed','contact') NOT NULL DEFAULT 'fixed',
    category_id         INT UNSIGNED NOT NULL,
    main_image          VARCHAR(255) NULL,
    is_featured         TINYINT(1) NOT NULL DEFAULT 0,
    is_active           TINYINT(1) NOT NULL DEFAULT 1,
    sort_order          INT UNSIGNED NOT NULL DEFAULT 0,
    meta_title          VARCHAR(255) NULL,
    meta_description    VARCHAR(500) NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_products_slug (slug),
    KEY idx_products_active_sort (is_active, sort_order),
    KEY idx_products_active_featured (is_active, is_featured),
    KEY idx_products_category (category_id),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_products_price_status CHECK (
        (price_status = 'fixed' AND price IS NOT NULL)
        OR (price_status = 'contact' AND price IS NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ------------------------------------------------------------
-- product_images (ürün başına ek galeri görselleri)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS product_images (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    image_path  VARCHAR(255) NOT NULL,
    alt_text    VARCHAR(255) NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    KEY idx_product_images_product_sort (product_id, sort_order),
    CONSTRAINT fk_product_images_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ------------------------------------------------------------
-- gallery_images (ürünlerden bağımsız genel site galerisi)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS gallery_images (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image_path  VARCHAR(255) NOT NULL,
    alt_text    VARCHAR(255) NULL,
    caption     VARCHAR(255) NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_gallery_images_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ------------------------------------------------------------
-- site_settings (esnek key-value site ayarları)
-- ------------------------------------------------------------
-- "key"/"value" MySQL'de ayrıştırma sorunu yaratabildiği için
-- setting_key / setting_value isimlendirmesi tercih edildi.
CREATE TABLE IF NOT EXISTS site_settings (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key   VARCHAR(100) NOT NULL,
    setting_value TEXT NULL,
    UNIQUE KEY uq_site_settings_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ------------------------------------------------------------
-- admin_users (yalnızca site yöneticisi hesapları — üyelik sistemi değil)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username       VARCHAR(60) NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    last_login_at  TIMESTAMP NULL DEFAULT NULL,
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admin_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Bu tabloya bilinçli olarak seed verisi eklenmedi.
-- Gerçek admin hesabı, kimlik doğrulama sistemiyle birlikte AŞAMA 2'de
-- güvenli şekilde (hash'lenmiş şifre) ayrı bir adım olarak oluşturulacak.

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- site_settings — TSİNAN Flowers gerçek işletme bilgileri
-- ------------------------------------------------------------
-- Doğrulanmamış alanlar (email, slogan, facebook) NULL bırakılmıştır —
-- admin panel "Site Ayarları" ekranından ileride girilebilir.

INSERT INTO site_settings (setting_key, setting_value) VALUES
    ('site_name', 'TSİNAN Flowers'),
    ('site_tagline', NULL),
    ('phone', '0532 626 28 89'),
    ('whatsapp', '0532 626 28 89'),
    ('email', NULL),
    ('address', 'Göksu, Sadri Alışık Cd. No:3, 34815 Beykoz/İstanbul'),
    ('working_hours', 'Pazartesi - Cumartesi: 09:00 - 19:00 / Pazar: 09:00 - 17:00'),
    ('instagram_url', 'https://www.instagram.com/tsinanflowers/'),
    ('facebook_url', NULL),
    ('telegram_url', 'https://t.me/tsinanflowers'),
    ('map_lat', '41.079039'),
    ('map_lng', '29.0738468'),
    ('logo_path', NULL),
    ('default_meta_title', 'TSİNAN Flowers | Beykoz Çiçekçi'),
    ('default_meta_description', 'TSİNAN Flowers, Göksu, Beykoz (İstanbul) adresinde hizmet veren çiçekçidir. Telefon veya WhatsApp üzerinden ürünler hakkında bilgi alabilirsiniz.');

-- ------------------------------------------------------------
-- categories — gerçek kategori yapısı
-- ------------------------------------------------------------
-- "Tören & Kutlama Çiçekleri" tür bilgisi teyit edilmediği için pasif
-- (is_active = 0) eklenir; admin panelden görülür ama sitede yayınlanmaz.

INSERT INTO categories (name, slug, sort_order, is_active) VALUES
    ('Buketler', 'buketler', 1, 1),
    ('Güller', 'guller', 2, 1),
    ('Orkideler', 'orkideler', 3, 1),
    ('Saksı Bitkileri', 'saksi-bitkileri', 4, 1),
    ('Özel Tasarımlar / Sanatsal Kompozisyonlar', 'ozel-tasarimlar', 5, 1),
    ('Tören & Kutlama Çiçekleri', 'toren-kutlama-cicekleri', 6, 0);
