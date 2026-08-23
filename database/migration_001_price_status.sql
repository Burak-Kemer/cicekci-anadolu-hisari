-- ============================================================
-- Migration 001 — Ürün fiyatı için "iletişime geçin" durumu
-- ============================================================
-- Amaç: Fiyatı henüz kesinleşmemiş gerçek ürünler için tahmini/uydurma
-- fiyat göstermek yerine, ürünü "fiyat bilgisi için iletişime geçin"
-- durumunda yayınlayabilmek.
--
-- Uygulama: schema.sql'i DAHA ÖNCE import etmiş, canlıda çalışan bir
-- veritabanına karşı BİR KEZ çalıştırılır. Yeni kurulumlarda buna
-- gerek yoktur — schema.sql zaten bu yapıyla güncellenmiştir.
-- products tablosu boşsa (bu projede olduğu gibi) veri kaybı riski yoktur.
-- ============================================================

ALTER TABLE products
    MODIFY COLUMN price DECIMAL(10,2) UNSIGNED NULL;

ALTER TABLE products
    ADD COLUMN price_status ENUM('fixed','contact') NOT NULL DEFAULT 'fixed' AFTER price;

-- Veri bütünlüğü: 'fixed' ise price dolu, 'contact' ise price boş olmalı.
ALTER TABLE products
    ADD CONSTRAINT chk_products_price_status CHECK (
        (price_status = 'fixed' AND price IS NOT NULL)
        OR (price_status = 'contact' AND price IS NULL)
    );
