<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../app/core/Session.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Auth.php';
require_once __DIR__ . '/../../app/core/Csrf.php';
require_once __DIR__ . '/../../app/core/Flash.php';
require_once __DIR__ . '/../../app/core/Slug.php';
require_once __DIR__ . '/../../app/core/ImageUploader.php';
require_once __DIR__ . '/../../app/core/LoginThrottle.php';
require_once __DIR__ . '/../../app/core/CategoryRepository.php';
require_once __DIR__ . '/../../app/core/ProductRepository.php';
require_once __DIR__ . '/../../app/core/GalleryRepository.php';
require_once __DIR__ . '/../../app/core/SettingsRepository.php';

Session::start();

$db = Database::getConnection();
