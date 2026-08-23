# Hero Görseli — Geçici Kaynak

Bu klasör, ana sayfa hero arka plan görseli için geçici bir hazırlık alanıdır.
Web sitesinden doğrudan servis edilmez (`public_html/` dışındadır).

Beklenen dosya: `tsinan-flowers-hero.webp`

## Production'a Aktarım

Gerçek dosya buraya eklendiğinde, production'da kullanılan gerçek konuma
kopyalanır:

```
public_html/assets/img/hero/tsinan-flowers-hero.webp
```

Bu yol, `public_html/assets/css/public.css` içindeki `.hero--home` kuralında
`background-image` olarak zaten tanımlıdır — dosya bu isimle production
klasörüne kopyalandığı an, hiçbir ek kod değişikliği gerekmeden hero'da
görünür.
