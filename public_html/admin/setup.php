<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$adminCount = (int) $db->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();

if ($adminCount > 0) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head><meta charset="UTF-8"><meta name="robots" content="noindex, nofollow"><title>Kurulum Tamamlandı</title></head>
    <body style="font-family:sans-serif;max-width:600px;margin:60px auto;padding:0 20px;">
        <h1>Kurulum Zaten Tamamlanmış</h1>
        <p>Sistemde en az bir admin hesabı zaten mevcut. Güvenlik nedeniyle bu kurulum ekranı devre dışıdır.</p>
        <p><strong>Lütfen bu dosyayı (admin/setup.php) sunucudan silin.</strong></p>
        <p><a href="/admin/login.php">Giriş sayfasına dön</a></p>
    </body>
    </html>
    <?php
    exit;
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Güvenlik doğrulaması başarısız oldu. Lütfen sayfayı yenileyip tekrar deneyin.';
    }

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

    if (mb_strlen($username) < 3) {
        $errors[] = 'Kullanıcı adı en az 3 karakter olmalıdır.';
    }

    if (mb_strlen($password) < 10) {
        $errors[] = 'Şifre en az 10 karakter olmalıdır.';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = 'Şifreler eşleşmiyor.';
    }

    if (empty($errors)) {
        $recheck = (int) $db->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();

        if ($recheck > 0) {
            $errors[] = 'Kurulum başka bir istek tarafından zaten tamamlanmış.';
        } else {
            $stmt = $db->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            ?>
            <!DOCTYPE html>
            <html lang="tr">
            <head><meta charset="UTF-8"><meta name="robots" content="noindex, nofollow"><title>Kurulum Tamamlandı</title></head>
            <body style="font-family:sans-serif;max-width:600px;margin:60px auto;padding:0 20px;">
                <h1>Admin Hesabı Oluşturuldu</h1>
                <p>Kullanıcı adı: <strong><?php echo htmlspecialchars($username); ?></strong></p>
                <p style="color:#b00020;"><strong>ÖNEMLİ:</strong> Güvenlik için bu dosyayı (admin/setup.php) şimdi sunucudan silin. Bu dosya silinmeden sistem canlıya alınmamalıdır.</p>
                <p><a href="/admin/login.php">Giriş sayfasına git</a></p>
            </body>
            </html>
            <?php
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İlk Kurulum — Admin Hesabı Oluştur</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body admin-body--auth">
<main class="admin-auth">
    <form class="admin-auth__form" method="post" action="/admin/setup.php" novalidate>
        <h1 class="admin-auth__title">İlk Admin Hesabını Oluştur</h1>
        <p>Bu ekran yalnızca bir kez kullanılabilir. Hesap oluşturulduktan sonra bu dosyayı silmeniz gerekir.</p>

        <?php foreach ($errors as $error): ?>
            <div class="admin-alert admin-alert--error"><?php echo htmlspecialchars($error); ?></div>
        <?php endforeach; ?>

        <?php echo Csrf::field(); ?>

        <label class="admin-field">
            <span>Kullanıcı Adı</span>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required minlength="3">
        </label>

        <label class="admin-field">
            <span>Şifre (en az 10 karakter)</span>
            <input type="password" name="password" required minlength="10">
        </label>

        <label class="admin-field">
            <span>Şifre (Tekrar)</span>
            <input type="password" name="password_confirm" required minlength="10">
        </label>

        <button type="submit" class="admin-btn admin-btn--primary admin-btn--block">Hesabı Oluştur</button>
    </form>
</main>
</body>
</html>
