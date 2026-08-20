-- ============================================================
-- Çiçekçi Web Sitesi — DEMO / PLACEHOLDER Veri (AŞAMA 1)
-- ============================================================
-- Bu dosya YALNIZCA test/geliştirme amaçlıdır. schema.sql'den
-- BAĞIMSIZDIR — istenirse hiç çalıştırılmayabilir.
--
-- İçerdiği tüm kategori ve ürünler DEMO'dur; gerçek işletme adı veya
-- sahte firma bilgisi İÇERMEZ. Gerçek ürünler eklenmeye başladığında
-- bu dosyayla gelen kayıtlar admin panel > Ürün Yönetimi üzerinden
-- kolayca silinebilir ("[DEMO]" öneki ile tek bakışta ayırt edilir).
--
-- main_image ve product_images kasıtlı olarak BOŞ bırakılmıştır:
-- gerçek görsel upload sistemi henüz kurulmadı (AŞAMA 3'te gelecek).
-- Var olmayan görsel dosyalarına sahte yol üretilmemiştir.
-- ============================================================

-- ------------------------------------------------------------
-- Demo kategoriler (5 adet)
-- ------------------------------------------------------------
INSERT INTO categories (name, slug, sort_order, is_active) VALUES
    ('Buketler', 'buketler', 1, 1),
    ('Güller', 'guller', 2, 1),
    ('Orkideler', 'orkideler', 3, 1),
    ('Saksı Çiçekleri', 'saksi-cicekleri', 4, 1),
    ('Özel Tasarımlar', 'ozel-tasarimlar', 5, 1);

-- ------------------------------------------------------------
-- Demo ürünler (8 adet, main_image = NULL — henüz görsel yok)
-- ------------------------------------------------------------
INSERT INTO products
    (name, slug, description, price, category_id, main_image, is_featured, is_active, sort_order)
VALUES
    ('[DEMO] Kırmızı Gül Buketi', 'demo-kirmizi-gul-buketi',
     'Bu bir DEMO/PLACEHOLDER üründür. Gerçek ürün verisi eklendiğinde admin panelden silinebilir.',
     450.00, (SELECT id FROM categories WHERE slug = 'guller'), NULL, 1, 1, 1),

    ('[DEMO] Beyaz Gül Demeti', 'demo-beyaz-gul-demeti',
     'Bu bir DEMO/PLACEHOLDER üründür. Gerçek ürün verisi eklendiğinde admin panelden silinebilir.',
     400.00, (SELECT id FROM categories WHERE slug = 'guller'), NULL, 0, 1, 2),

    ('[DEMO] Mevsim Çiçekleri Buketi', 'demo-mevsim-cicekleri-buketi',
     'Bu bir DEMO/PLACEHOLDER üründür. Gerçek ürün verisi eklendiğinde admin panelden silinebilir.',
     350.00, (SELECT id FROM categories WHERE slug = 'buketler'), NULL, 1, 1, 1),

    ('[DEMO] Pastel Tonlu Karışık Buket', 'demo-pastel-tonlu-karisik-buket',
     'Bu bir DEMO/PLACEHOLDER üründür. Gerçek ürün verisi eklendiğinde admin panelden silinebilir.',
     500.00, (SELECT id FROM categories WHERE slug = 'buketler'), NULL, 0, 1, 2),

    ('[DEMO] Phalaenopsis Orkide Saksısı', 'demo-phalaenopsis-orkide-saksisi',
     'Bu bir DEMO/PLACEHOLDER üründür. Gerçek ürün verisi eklendiğinde admin panelden silinebilir.',
     600.00, (SELECT id FROM categories WHERE slug = 'orkideler'), NULL, 1, 1, 1),

    ('[DEMO] Mini Orkide Aranjmanı', 'demo-mini-orkide-aranjmani',
     'Bu bir DEMO/PLACEHOLDER üründür. Gerçek ürün verisi eklendiğinde admin panelden silinebilir.',
     380.00, (SELECT id FROM categories WHERE slug = 'orkideler'), NULL, 0, 1, 2),

    ('[DEMO] Yeşil Saksı Bitkisi', 'demo-yesil-saksi-bitkisi',
     'Bu bir DEMO/PLACEHOLDER üründür. Gerçek ürün verisi eklendiğinde admin panelden silinebilir.',
     300.00, (SELECT id FROM categories WHERE slug = 'saksi-cicekleri'), NULL, 0, 1, 1),

    ('[DEMO] Özel Tasarım Düğün Aranjmanı', 'demo-ozel-tasarim-dugun-aranjmani',
     'Bu bir DEMO/PLACEHOLDER üründür. Gerçek ürün verisi eklendiğinde admin panelden silinebilir.',
     1200.00, (SELECT id FROM categories WHERE slug = 'ozel-tasarimlar'), NULL, 1, 1, 1);

-- product_images ve gallery_images tabloları bilinçli olarak BOŞ bırakılmıştır
-- (henüz gerçek/placeholder görsel dosyası yok).
