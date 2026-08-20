<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Admin Panel'); ?> — <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar__brand"><?php echo htmlspecialchars(SITE_NAME); ?></div>
        <nav class="admin-nav">
            <a href="/admin/index.php" class="admin-nav__link<?php echo ($activeMenu ?? '') === 'dashboard' ? ' is-active' : ''; ?>">Dashboard</a>
            <a href="/admin/urunler/liste.php" class="admin-nav__link<?php echo ($activeMenu ?? '') === 'urunler' ? ' is-active' : ''; ?>">Ürünler</a>
            <a href="/admin/kategoriler/liste.php" class="admin-nav__link<?php echo ($activeMenu ?? '') === 'kategoriler' ? ' is-active' : ''; ?>">Kategoriler</a>
            <a href="/admin/galeri/liste.php" class="admin-nav__link<?php echo ($activeMenu ?? '') === 'galeri' ? ' is-active' : ''; ?>">Galeri</a>
            <a href="/admin/ayarlar/index.php" class="admin-nav__link<?php echo ($activeMenu ?? '') === 'ayarlar' ? ' is-active' : ''; ?>">Site Ayarları</a>
        </nav>
        <div class="admin-sidebar__footer">
            <a href="/" target="_blank" rel="noopener" class="admin-nav__link">Siteyi Görüntüle</a>
            <a href="/admin/logout.php" class="admin-nav__link admin-nav__link--danger">Çıkış Yap</a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button type="button" class="admin-topbar__menu-toggle" id="adminMenuToggle" aria-label="Menüyü aç/kapat" aria-expanded="false">☰</button>
            <h1 class="admin-topbar__title"><?php echo htmlspecialchars($pageTitle ?? 'Admin Panel'); ?></h1>
            <span class="admin-topbar__user"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? ''); ?></span>
        </header>

        <main class="admin-content">
            <?php foreach (Flash::all() as $flash): ?>
                <div class="admin-alert admin-alert--<?php echo htmlspecialchars($flash['type']); ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endforeach; ?>
