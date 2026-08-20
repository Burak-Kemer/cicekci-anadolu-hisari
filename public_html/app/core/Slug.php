<?php

declare(strict_types=1);

class Slug
{
    public static function generate(string $text): string
    {
        $map = [
            'ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g', 'ı' => 'i', 'İ' => 'i',
            'ö' => 'o', 'Ö' => 'o', 'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u',
        ];

        $text = strtr($text, $map);
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        $text = trim($text, '-');

        return $text !== '' ? $text : 'kayit';
    }

    public static function unique(PDO $db, string $table, string $baseSlug, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $suffix = 2;

        while (self::exists($db, $table, $slug, $excludeId)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private static function exists(PDO $db, string $table, string $slug, ?int $excludeId): bool
    {
        $allowedTables = ['categories', 'products'];
        if (!in_array($table, $allowedTables, true)) {
            throw new InvalidArgumentException('Gecersiz tablo adi.');
        }

        $sql = "SELECT COUNT(*) FROM {$table} WHERE slug = ?";
        $params = [$slug];

        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }
}
