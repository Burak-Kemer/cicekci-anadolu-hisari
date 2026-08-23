# TSİNAN Flowers — Geçici Görsel Hazırlık Alanı

Bu klasör **geçici bir kaynak/hazırlık alanıdır**. Production web sitesinin
bir parçası değildir:

- Web sitesinden **doğrudan servis edilmez** — `public_html/` dışındadır,
  tarayıcıdan hiçbir şekilde erişilemez.
- Mevcut `ImageUploader` sınıfı, admin panel upload akışı, `.htaccess`
  kuralları veya veritabanı bu klasörden **habersizdir ve etkilenmez**.
- Yalnızca gerçek TSİNAN Flowers ürün görsellerinin manuel olarak indirilip
  düzenlenmesi ve düzenli şekilde numaralandırılması için kullanılır.

## Klasör Yapısı

```
assets-source/tsinan-flowers/products/urun-01/ ... urun-11/
```

Her `urun-XX` klasörü, ileride gerçek bir ürünle eşleştirilecek bir görsel
setini temsil eder. Her birinin içindeki `README.md` şu an "BEKLİYOR" olarak
işaretli alanları (ürün adı, slug, kategori, fiyat durumu) içerir — bu
bilgiler gerçek ürün verisi (veri paketi) sisteme uygulandığında doldurulacak.

## Sonraki Adım — Production'a Aktarım

Görseller hazırlandıktan ve gerçek ürün kayıtları (adı/slug/kategori)
veritabanına eklendikten sonra, bu klasördeki görseller **mevcut güvenli
upload sistemi üzerinden** production'a taşınacak — bu klasörden doğrudan
kopyalanmayacak veya veritabanına elle yol yazılmayacaktır. Öneri: admin
panel > Ürünler > Düzenle ekranındaki "Ana Görsel" / "Ek Görseller" upload
alanlarından, bu klasördeki dosyalar seçilerek yüklenir — böylece mevcut
`ImageUploader` sınıfının finfo MIME kontrolü, rastgele dosya adı üretimi ve
GD ile yeniden boyutlandırma adımlarının hiçbiri atlanmaz.
