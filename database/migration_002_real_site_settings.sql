-- ============================================================
-- Migration 002 — TSİNAN Flowers gerçek işletme bilgileri
-- ============================================================
-- schema.sql'in sonunda import edilmiş olan PLACEHOLDER site_settings
-- satırlarını, doğrulanmış gerçek TSİNAN Flowers bilgileriyle değiştirir.
-- Doğrulanmamış hiçbir alan (email, slogan, facebook) doldurulmamıştır —
-- bunlar boş/NULL bırakılmıştır ve admin panel > Site Ayarları üzerinden
-- ileride girilebilir.
--
-- INSERT ... ON DUPLICATE KEY UPDATE kullanır (setting_key UNIQUE), bu
-- yüzden idempotenttir ve hem "hiç satır yok" hem "placeholder satır var"
-- durumlarında güvenle çalışır.
--
-- map_lat / map_lng: schema.sql'de tanımlı değildi, bu migration ile
-- yeni key olarak eklenir (site_settings esnek key-value yapısı sayesinde
-- ALTER TABLE gerekmez).
-- ============================================================

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
    ('default_meta_description', 'TSİNAN Flowers, Göksu, Beykoz (İstanbul) adresinde hizmet veren çiçekçidir. Telefon veya WhatsApp üzerinden ürünler hakkında bilgi alabilirsiniz.')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
