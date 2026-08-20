-- ============================================================
-- Çiçekçi Web Sitesi — Veritabanı Şeması (AŞAMA 1)
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
    price               DECIMAL(10,2) UNSIGNED NOT NULL,
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
        ON DELETE RESTRICT ON UPDATE CASCADE
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
-- site_settings — placeholder temel satırlar
-- ------------------------------------------------------------
-- Bu değerler UYDURULMAMIŞTIR — hepsi açıkça PLACEHOLDER olarak
-- işaretlenmiştir. Gerçek işletme bilgileri geldiğinde admin panelin
-- "Site Ayarları" ekranından (AŞAMA 6) güncellenecektir.

INSERT INTO site_settings (setting_key, setting_value) VALUES
    ('site_name', 'PLACEHOLDER - Çiçekçi İşletme Adı'),
    ('site_tagline', 'PLACEHOLDER - Marka sloganı'),
    ('phone', 'PLACEHOLDER - Telefon numarası'),
    ('whatsapp', 'PLACEHOLDER - WhatsApp numarası (örn: 905XXXXXXXXX)'),
    ('email', 'PLACEHOLDER - E-posta adresi'),
    ('address', 'PLACEHOLDER - Anadolu Hisarı, İstanbul (tam adres girilecek)'),
    ('working_hours', 'PLACEHOLDER - Çalışma saatleri (örn: Pzt-Cmt 09:00-19:00)'),
    ('instagram_url', 'PLACEHOLDER - Instagram bağlantısı'),
    ('facebook_url', 'PLACEHOLDER - Facebook bağlantısı'),
    ('logo_path', NULL),
    ('default_meta_title', 'PLACEHOLDER - Varsayılan SEO başlığı'),
    ('default_meta_description', 'PLACEHOLDER - Varsayılan SEO açıklaması');
