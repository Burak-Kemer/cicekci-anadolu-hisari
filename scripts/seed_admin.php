<?php

declare(strict_types=1);

// CLI-only: admin_users tablosuna bir hesap ekler veya günceller.
//
// Şifre hiçbir dosyaya (bu script dahil) düz metin olarak YAZILMAZ —
// yalnızca ADMIN_SEED_PASSWORD ortam değişkeninden okunur, password_hash()
// ile hash'lenir ve sadece hash veritabanına kaydedilir.
//
// Kullanım:
//   ADMIN_SEED_PASSWORD='...gizli şifre...' php scripts/seed_admin.php <kullanici_adi>
//
// Kullanıcı zaten varsa (aynı username) şifre hash'i güvenle güncellenir
// (INSERT ... ON DUPLICATE KEY UPDATE, admin_users.username UNIQUE).

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Bu betik yalnızca komut satırından (CLI) çalıştırılabilir.');
}

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../public_html/app/core/Database.php';

$username = trim((string) ($argv[1] ?? ''));
if ($username === '') {
    fwrite(STDERR, "Kullanım: php scripts/seed_admin.php <kullanici_adi>\n");
    fwrite(STDERR, "(Şifre ADMIN_SEED_PASSWORD ortam değişkeninden okunur.)\n");
    exit(1);
}

$password = getenv('ADMIN_SEED_PASSWORD');
if ($password === false || $password === '') {
    fwrite(STDERR, "Hata: ADMIN_SEED_PASSWORD ortam değişkeni ayarlanmamış.\n");
    exit(1);
}

if (mb_strlen($password) < 10) {
    fwrite(STDERR, "Hata: şifre en az 10 karakter olmalıdır.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$password = null;
unset($password);

$db = Database::getConnection();
$stmt = $db->prepare(
    'INSERT INTO admin_users (username, password_hash) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)'
);
$stmt->execute([$username, $hash]);

echo "Admin kullanıcısı '{$username}' oluşturuldu/güncellendi.\n";
