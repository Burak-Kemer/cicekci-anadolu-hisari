<?php

declare(strict_types=1);

class SettingsRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $result = [];
        foreach ($this->db->query('SELECT setting_key, setting_value FROM site_settings') as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }

        return $result;
    }

    public function update(array $values): void
    {
        $stmt = $this->db->prepare('UPDATE site_settings SET setting_value = ? WHERE setting_key = ?');
        foreach ($values as $key => $value) {
            $stmt->execute([$value, $key]);
        }
    }

    /**
     * Aşama 1'de seed edilen "PLACEHOLDER - ..." değerlerini tanır. Public
     * frontend, henüz gerçek veri girilmemiş alanlar için bozuk link/gömme
     * yerine zarif fallback göstermek için bunu kullanır.
     */
    public static function isPlaceholder(?string $value): bool
    {
        $trimmed = trim((string) $value);

        return $trimmed === '' || str_starts_with($trimmed, 'PLACEHOLDER');
    }

    /**
     * Tüm public sayfalarda tutarlı site adı çözümü: gerçek değer varsa onu,
     * yoksa config.php'deki SITE_NAME sabitini kullanır. Tek nokta — sayfalara
     * işletme adı elle tekrar yazılmaz.
     */
    public static function siteName(array $settings): string
    {
        return self::isPlaceholder($settings['site_name'] ?? null)
            ? SITE_NAME
            : (string) $settings['site_name'];
    }

    /**
     * Gerçek bir WhatsApp numarası varsa hazır mesajla wa.me linki üretir;
     * numara placeholder/boşsa null döner (çağıran taraf fallback gösterir).
     */
    public static function whatsAppLink(?string $rawNumber, string $message): ?string
    {
        if (self::isPlaceholder($rawNumber)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $rawNumber);
        if ($digits === null || $digits === '') {
            return null;
        }

        // wa.me, ülke koduyla başlayan TAM uluslararası format bekler (90...).
        // Türkiye'de yerel format ("0532...") başında ülke kodu değil, ulusal
        // erişim öneki "0" taşır — bu, doğrudan wa.me'ye verilirse geçersiz/
        // kırık bir link üretir. 0 ile başlayan 11 haneli TR mobil numaralarda
        // baştaki 0'ı ülke koduyla (90) değiştiriyoruz.
        if (strlen($digits) === 11 && $digits[0] === '0') {
            $digits = '90' . substr($digits, 1);
        }

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($message);
    }
}
