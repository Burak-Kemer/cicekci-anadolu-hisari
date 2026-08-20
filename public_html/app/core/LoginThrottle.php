<?php

declare(strict_types=1);

// Basit, dosya tabanli server-side login rate limiting.
// storage/ klasoru public_html DISINDADIR, web'den erisilemez.

class LoginThrottle
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 900; // 15 dakika

    public function __construct(private string $storageFile)
    {
    }

    public function isLocked(string $identifier): bool
    {
        $data = $this->read($identifier);
        if ($data === null || $data['count'] < self::MAX_ATTEMPTS) {
            return false;
        }

        return (time() - $data['last_attempt']) < self::LOCKOUT_SECONDS;
    }

    public function remainingLockSeconds(string $identifier): int
    {
        $data = $this->read($identifier);
        if ($data === null) {
            return 0;
        }

        return max(0, self::LOCKOUT_SECONDS - (time() - $data['last_attempt']));
    }

    public function registerFailure(string $identifier): void
    {
        $all = $this->readAll();
        $current = $all[$identifier] ?? ['count' => 0, 'last_attempt' => 0];

        if ((time() - $current['last_attempt']) > self::LOCKOUT_SECONDS) {
            $current['count'] = 0;
        }

        $current['count']++;
        $current['last_attempt'] = time();
        $all[$identifier] = $current;

        $this->writeAll($all);
    }

    public function clear(string $identifier): void
    {
        $all = $this->readAll();
        unset($all[$identifier]);
        $this->writeAll($all);
    }

    private function read(string $identifier): ?array
    {
        return $this->readAll()[$identifier] ?? null;
    }

    private function readAll(): array
    {
        if (!is_file($this->storageFile)) {
            return [];
        }

        $content = file_get_contents($this->storageFile);
        $data = json_decode($content !== false ? $content : '[]', true);

        return is_array($data) ? $data : [];
    }

    private function writeAll(array $data): void
    {
        $dir = dirname($this->storageFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->storageFile, json_encode($data), LOCK_EX);
    }
}
