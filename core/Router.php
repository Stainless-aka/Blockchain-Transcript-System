<?php

/**
 * Router — maps URIs to Controller@method handlers.
 */
class Router
{
    private array $routes = [];
    private string $prefix = '';
    private array $groupMiddleware = [];

    /* ------------------------------------------------------------------ */
    /*  Route registration                                                  */
    /* ------------------------------------------------------------------ */

    public function get(string $path, string $handler, array $mw = []): void
    {
        $this->add('GET', $path, $handler, $mw);
    }

    public function post(string $path, string $handler, array $mw = []): void
    {
        $this->add('POST', $path, $handler, $mw);
    }

    private function add(string $method, string $path, string $handler, array $mw): void
    {
        $full    = rtrim($this->prefix . $path, '/') ?: '/';
        $pattern = '#^' . preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $full) . '$#';
        $this->routes[] = [
            'method'   => $method,
            'pattern'  => $pattern,
            'handler'  => $handler,
            'mw'       => array_merge($this->groupMiddleware, $mw),
        ];
    }

    /** Group routes under a path prefix with shared middleware */
    public function group(string $prefix, callable $cb, array $mw = []): void
    {
        $prev       = $this->prefix;
        $prevMw     = $this->groupMiddleware;
        $this->prefix             = $prev . $prefix;
        $this->groupMiddleware    = array_merge($prevMw, $mw);
        $cb($this);
        $this->prefix           = $prev;
        $this->groupMiddleware  = $prevMw;
    }

    /* ------------------------------------------------------------------ */
    /*  Dispatch                                                            */
    /* ------------------------------------------------------------------ */

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip the base path so the router works in a subdirectory
        $base = rtrim(parse_url(BASE_URL, PHP_URL_PATH) ?? '', '/');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            if (!preg_match($route['pattern'], $uri, $m)) continue;

            // Named params
            $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);

            // Run middleware
            foreach ($route['mw'] as $mwClass) {
                $this->runMiddleware($mwClass);
            }

            // Call controller
            [$ctrl, $action] = explode('@', $route['handler']);
            $this->invoke($ctrl, $action, $params);
            return;
        }

        $this->notFound();
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function runMiddleware(string $cls): void
    {
        require_once BASE_PATH . '/app/middleware/' . $cls . '.php';
        (new $cls())->handle();
    }

    private function invoke(string $ctrl, string $action, array $params): void
    {
        require_once BASE_PATH . '/app/controllers/' . $ctrl . '.php';
        if (!class_exists($ctrl))          die("Controller class not found: {$ctrl}");
        $obj = new $ctrl();
        if (!method_exists($obj, $action)) die("Action not found: {$ctrl}@{$action}");
        $obj->$action($params);
    }

    private function notFound(): void
    {
        http_response_code(404);
        $f = BASE_PATH . '/app/views/errors/404.php';
        file_exists($f) ? require $f : print '<h1>404 — Page Not Found</h1>';
    }
}
