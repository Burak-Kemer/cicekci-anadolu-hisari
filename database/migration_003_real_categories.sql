-- ============================================================
-- Migration 003 — Gerçek kategori yapısı
-- ============================================================
-- categories tablosu şu an boş (demo veri hiç import edilmemişti).
-- Talimatta belirtilen 5 aktif + 1 pasif kategori burada oluşturulur.
-- "Tören & Kutlama Çiçekleri" tür bilgisi teyit edilmediği için is_active=0
-- ile pasif eklenir (admin panelden görülebilir ama sitede yayınlanmaz).
--
-- INSERT ... ON DUPLICATE KEY UPDATE kullanır (slug UNIQUE), idempotenttir.
-- ============================================================

INSERT INTO categories (name, slug, sort_order, is_active) VALUES
    ('Buketler', 'buketler', 1, 1),
    ('Güller', 'guller', 2, 1),
    ('Orkideler', 'orkideler', 3, 1),
    ('Saksı Bitkileri', 'saksi-bitkileri', 4, 1),
    ('Özel Tasarımlar / Sanatsal Kompozisyonlar', 'ozel-tasarimlar', 5, 1),
    ('Tören & Kutlama Çiçekleri', 'toren-kutlama-cicekleri', 6, 0)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    sort_order = VALUES(sort_order),
    is_active = VALUES(is_active);
