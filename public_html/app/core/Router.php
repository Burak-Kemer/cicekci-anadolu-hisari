<?php

declare(strict_types=1);

// GET route kaydı + dispatch. `{param}` şeklindeki segmentler dinamik kabul
// edilir (ör. '/urun/{slug}') ve handler'a ilişkilendirilmiş dizi olarak geçilir.

class Router
{
    /** @var array<int, array{pattern: string, handler: callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = ['pattern' => $pattern, 'handler' => $handler];
    }

    /**
     * Eşleşen bir route bulunup çalıştırılırsa true, bulunamazsa false döner
     * (çağıran taraf false durumunda kendi 404 sayfasını render eder).
     */
    public function dispatch(string $requestUri): bool
    {
        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            $params = $this->match($route['pattern'], $path);
            if ($params !== null) {
                ($route['handler'])($params);

                return true;
            }
        }

        return false;
    }

    /**
     * Statik kısımlar (ör. '/robots.txt' içindeki '.') preg_quote ile
     * kaçışlanır; '{param}' segmentleri geçici bir işaretleyiciyle korunup
     * kaçışlama sonrası capture group'a çevrilir. Böylece bir route pattern'i
     * içindeki nokta gibi regex özel karakterleri "herhangi bir karakter"
     * anlamına gelmez, yalnızca kendisiyle eşleşir.
     *
     * @return array<string, string>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        $paramNames = [];
        $marked = preg_replace_callback(
            '#\{([a-zA-Z_]+)\}#',
            function (array $m) use (&$paramNames): string {
                $paramNames[] = $m[1];

                return '@@PARAM@@';
            },
            $pattern
        );

        if ($marked === null) {
            return null;
        }

        $regex = str_replace('@@PARAM@@', '([^/]+)', preg_quote($marked, '#'));

        if (preg_match('#^' . $regex . '$#u', $path, $matches) !== 1) {
            return null;
        }

        $params = [];
        foreach ($paramNames as $index => $name) {
            $params[$name] = $matches[$index + 1] ?? '';
        }

        return $params;
    }
}
