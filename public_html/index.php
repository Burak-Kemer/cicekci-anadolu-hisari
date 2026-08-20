<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/CategoryRepository.php';
require_once __DIR__ . '/app/core/ProductRepository.php';
require_once __DIR__ . '/app/core/GalleryRepository.php';
require_once __DIR__ . '/app/core/SettingsRepository.php';
require_once __DIR__ . '/app/core/Seo.php';
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/ProductController.php';
require_once __DIR__ . '/app/controllers/GalleryController.php';
require_once __DIR__ . '/app/controllers/PageController.php';
require_once __DIR__ . '/app/controllers/SitemapController.php';

$db = Database::getConnection();
$settings = (new SettingsRepository($db))->all();

$router = new Router();

$router->get('/', function () use ($db, $settings) {
    HomeController::index($db, $settings);
});

$router->get('/cicekler', function () use ($db, $settings) {
    if (!ProductController::catalog($db, $settings, null)) {
        http_response_code(404);
        require __DIR__ . '/app/views/errors/404.php';
    }
});

$router->get('/cicekler/{kategori}', function (array $params) use ($db, $settings) {
    if (!ProductController::catalog($db, $settings, $params['kategori'])) {
        http_response_code(404);
        require __DIR__ . '/app/views/errors/404.php';
    }
});

$router->get('/urun/{slug}', function (array $params) use ($db, $settings) {
    if (!ProductController::show($db, $settings, $params['slug'])) {
        http_response_code(404);
        require __DIR__ . '/app/views/errors/404.php';
    }
});

$router->get('/galeri', function () use ($db, $settings) {
    GalleryController::index($db, $settings);
});

$router->get('/hakkimizda', function () use ($settings) {
    PageController::about($settings);
});

$router->get('/iletisim', function () use ($settings) {
    PageController::contact($settings);
});

$router->get('/robots.txt', function () {
    SitemapController::robotsTxt();
});

$router->get('/sitemap.xml', function () use ($db) {
    SitemapController::sitemap($db);
});

if (!$router->dispatch($_SERVER['REQUEST_URI'])) {
    http_response_code(404);
    require __DIR__ . '/app/views/errors/404.php';
}
