<?php

declare(strict_types=1);

// Ürün, galeri ve site logosu için ortak, güvenli görsel yükleme sistemi.
// Gerçek MIME doğrulaması, rastgele dosya adı, GD ile thumb/large üretimi
// (varsa), ve path-traversal korumalı silme içerir.

class ImageUploader
{
    public const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private const SIZES = [
        'thumb' => 400,
        'large' => 1200,
    ];

    private string $uploadsRoot;

    public function __construct(string $uploadsRoot)
    {
        $this->uploadsRoot = rtrim($uploadsRoot, '/\\');
    }

    /**
     * @param array{name:string,type:string,tmp_name:string,error:int,size:int} $file
     * @return array{path: string}
     */
    public function upload(array $file, string $subfolder): array
    {
        $this->assertValidUpload($file);

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if ($mime === false || !isset(self::ALLOWED_MIME_TYPES[$mime])) {
            throw new RuntimeException('Desteklenmeyen dosya türü. Sadece JPEG, PNG veya WebP yükleyebilirsiniz.');
        }

        $extension = self::ALLOWED_MIME_TYPES[$mime];
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        $targetDir = $this->uploadsRoot . DIRECTORY_SEPARATOR . trim($subfolder, '/\\');
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException('Hedef klasör oluşturulamadı.');
        }

        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Dosya sunucuya taşınamadı.');
        }

        if (extension_loaded('gd')) {
            $this->generateResizedVersions($targetPath, $targetDir, $filename, $extension);
        }

        $relativePath = '/uploads/' . trim($subfolder, '/\\') . '/' . $filename;

        return ['path' => $relativePath];
    }

    public function delete(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $realUploadsRoot = realpath($this->uploadsRoot);
        if ($realUploadsRoot === false) {
            return;
        }

        $withinUploads = ltrim(str_replace('/uploads/', '', $relativePath), '/\\');
        $candidatePath = $this->uploadsRoot . DIRECTORY_SEPARATOR . $withinUploads;
        $targetPath = realpath($candidatePath);

        if ($targetPath === false) {
            return; // dosya zaten yok, sessizce geç
        }

        if (strpos($targetPath, $realUploadsRoot) !== 0) {
            throw new RuntimeException('Geçersiz dosya yolu.');
        }

        if (is_file($targetPath)) {
            unlink($targetPath);
        }

        $pathInfo = pathinfo($targetPath);
        if (!isset($pathInfo['extension'])) {
            return;
        }

        foreach (array_keys(self::SIZES) as $label) {
            $variant = $pathInfo['dirname'] . DIRECTORY_SEPARATOR
                . $pathInfo['filename'] . '-' . $label . '.' . $pathInfo['extension'];
            if (is_file($variant)) {
                unlink($variant);
            }
        }
    }

    public static function gdAvailable(): bool
    {
        return extension_loaded('gd');
    }

    public static function webpSupported(): bool
    {
        return extension_loaded('gd') && function_exists('imagewebp');
    }

    private function assertValidUpload(array $file): void
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Geçersiz dosya yükleme isteği.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('Dosya seçilmedi.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Dosya boyutu çok büyük.');
            default:
                throw new RuntimeException('Dosya yüklenirken bir hata oluştu.');
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            $maxMb = (int) (self::MAX_FILE_SIZE / 1024 / 1024);
            throw new RuntimeException("Dosya boyutu izin verilen sınırı aşıyor (maksimum {$maxMb} MB).");
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Geçersiz yükleme kaynağı.');
        }
    }

    private function generateResizedVersions(string $sourcePath, string $targetDir, string $filename, string $extension): void
    {
        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            return; // GD ile okunamıyorsa orijinal dosya ana görsel olarak kullanılmaya devam eder.
        }

        [$width, $height, $type] = $imageInfo;
        $source = $this->createImageResource($sourcePath, $type);
        if ($source === null) {
            return;
        }

        foreach (self::SIZES as $label => $maxDimension) {
            if ($width <= $maxDimension && $height <= $maxDimension) {
                continue; // görsel zaten küçükse büyütülmez
            }

            $resized = $this->resizeImage($source, $width, $height, $maxDimension);
            $sizedFilename = pathinfo($filename, PATHINFO_FILENAME) . '-' . $label . '.' . $extension;
            $this->saveImage($resized, $targetDir . DIRECTORY_SEPARATOR . $sizedFilename, $extension);
            imagedestroy($resized);
        }

        imagedestroy($source);
    }

    private function createImageResource(string $path, int $type)
    {
        $resource = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        return $resource !== false ? $resource : null;
    }

    private function resizeImage($source, int $width, int $height, int $maxDimension)
    {
        $ratio = min($maxDimension / $width, $maxDimension / $height, 1);
        $newWidth = (int) round($width * $ratio);
        $newHeight = (int) round($height * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $resized;
    }

    private function saveImage($resource, string $path, string $extension): void
    {
        switch ($extension) {
            case 'jpg':
                imagejpeg($resource, $path, 82);
                break;
            case 'png':
                imagepng($resource, $path, 6);
                break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    imagewebp($resource, $path, 82);
                } else {
                    $fallback = preg_replace('/\.webp$/', '.jpg', $path);
                    imagejpeg($resource, $fallback !== null ? $fallback : $path, 82);
                }
                break;
        }
    }
}
