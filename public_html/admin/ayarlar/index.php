<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth-check.php';

$settingsRepository = new SettingsRepository($db);
$settings = $settingsRepository->all();

$errors = [];

$fields = [
    'site_name' => 'Site/İşletme Adı',
    'site_tagline' => 'Site Sloganı',
    'phone' => 'Telefon',
    'whatsapp' => 'WhatsApp Numarası',
    'email' => 'E-posta',
    'address' => 'Adres',
    'working_hours' => 'Çalışma Saatleri',
    'instagram_url' => 'Instagram Bağlantısı',
    'facebook_url' => 'Facebook Bağlantısı',
    'telegram_url' => 'Telegram Bağlantısı',
    'default_meta_title' => 'Varsayılan SEO Başlığı',
    'default_meta_description' => 'Varsayılan SEO Açıklaması',
];

// Harita koordinatları ayrı tutulur: sayısal (float) doğrulama gerektirir,
// diğer alanlar gibi serbest metin değildir.
$mapFields = [
    'map_lat' => 'Harita Enlem (Latitude)',
    'map_lng' => 'Harita Boylam (Longitude)',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.';
    }

    $updated = [];
    foreach ($fields as $key => $label) {
        $updated[$key] = trim((string) ($_POST[$key] ?? ''));
    }

    foreach ($mapFields as $key => $label) {
        $rawValue = trim((string) ($_POST[$key] ?? ''));
        if ($rawValue !== '' && !is_numeric($rawValue)) {
            $errors[] = $label . ': geçerli bir sayı giriniz.';
            continue;
        }
        $updated[$key] = $rawValue;
    }

    if (empty($errors) && !empty($_FILES['logo']['name'])) {
        $uploader = new ImageUploader(__DIR__ . '/../../uploads');
        try {
            $result = $uploader->upload($_FILES['logo'], 'site');

            if (!empty($settings['logo_path'])) {
                $uploader->delete($settings['logo_path']);
            }

            $updated['logo_path'] = $result['path'];
        } catch (RuntimeException $e) {
            $errors[] = 'Logo: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        $settingsRepository->update($updated);
        Flash::success('Site ayarları güncellendi.');
        header('Location: /admin/ayarlar/index.php');
        exit;
    }

    $settings = array_merge($settings, $updated);
}

$pageTitle = 'Site Ayarları';
$activeMenu = 'ayarlar';

require __DIR__ . '/../includes/admin-header.php';
?>
<form method="post" action="/admin/ayarlar/index.php" enctype="multipart/form-data" class="admin-form">
    <?php echo Csrf::field(); ?>

    <?php foreach ($errors as $error): ?>
        <div class="admin-alert admin-alert--error"><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>

    <fieldset class="admin-fieldset">
        <legend>Logo</legend>
        <?php if (!empty($settings['logo_path'])): ?>
            <div class="admin-current-image">
                <img src="<?php echo htmlspecialchars($settings['logo_path']); ?>" alt="Logo" class="admin-thumb">
            </div>
        <?php endif; ?>
        <label class="admin-field">
            <span>Logoyu Değiştir</span>
            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp">
        </label>
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend>İşletme Bilgileri</legend>
        <?php foreach ($fields as $key => $label): ?>
            <label class="admin-field">
                <span><?php echo htmlspecialchars($label); ?></span>
                <?php if (in_array($key, ['address', 'default_meta_description'], true)): ?>
                    <textarea name="<?php echo $key; ?>" rows="3"><?php echo htmlspecialchars((string) ($settings[$key] ?? '')); ?></textarea>
                <?php else: ?>
                    <input type="text" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars((string) ($settings[$key] ?? '')); ?>">
                <?php endif; ?>
            </label>
        <?php endforeach; ?>
    </fieldset>

    <fieldset class="admin-fieldset">
        <legend>Harita Konumu</legend>
        <p>İletişim sayfasındaki harita ve yol tarifi bağlantısı için kullanılır. Boş bırakılırsa harita adres metnine göre gösterilir.</p>
        <?php foreach ($mapFields as $key => $label): ?>
            <label class="admin-field">
                <span><?php echo htmlspecialchars($label); ?></span>
                <input type="text" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars((string) ($settings[$key] ?? '')); ?>" inputmode="decimal">
            </label>
        <?php endforeach; ?>
    </fieldset>

    <div class="admin-form__actions">
        <button type="submit" class="admin-btn admin-btn--primary">Kaydet</button>
    </div>
</form>
<?php
require __DIR__ . '/../includes/admin-footer.php';
