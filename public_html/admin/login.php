<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (Auth::check()) {
    header('Location: /admin/index.php');
    exit;
}

$throttle = new LoginThrottle(__DIR__ . '/../../storage/login_attempts.json');
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $identifier = ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . ':' . mb_strtolower($username);

    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $error = 'Güvenlik doğrulaması başarısız oldu. Lütfen sayfayı yenileyip tekrar deneyin.';
    } elseif ($throttle->isLocked($identifier)) {
        $minutes = (int) ceil($throttle->remainingLockSeconds($identifier) / 60);
        $error = "Çok fazla başarısız deneme yapıldı. Lütfen yaklaşık {$minutes} dakika sonra tekrar deneyin.";
    } elseif ($username === '' || $password === '') {
        $error = 'Kullanıcı adı ve şifre gereklidir.';
    } elseif (Auth::attempt($db, $username, $password)) {
        $throttle->clear($identifier);
        header('Location: /admin/index.php');
        exit;
    } else {
        $throttle->registerFailure($identifier);
        $error = 'Kullanıcı adı veya şifre hatalı.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi — <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body admin-body--auth">
<main class="admin-auth">
    <form class="admin-auth__form" method="post" action="/admin/login.php" novalidate>
        <h1 class="admin-auth__title">Yönetici Girişi</h1>

        <?php if ($error !== null): ?>
            <div class="admin-alert admin-alert--error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php echo Csrf::field(); ?>

        <label class="admin-field">
            <span>Kullanıcı Adı</span>
            <input type="text" name="username" autocomplete="username" required autofocus>
        </label>

        <label class="admin-field">
            <span>Şifre</span>
            <input type="password" name="password" autocomplete="current-password" required>
        </label>

        <button type="submit" class="admin-btn admin-btn--primary admin-btn--block">Giriş Yap</button>
    </form>
</main>
</body>
</html>
