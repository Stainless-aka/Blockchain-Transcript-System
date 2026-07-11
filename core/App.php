<?php

/**
 * Application Bootstrap
 * Sets constants, loads env, starts session, registers autoloader, runs router.
 */
class App
{
    public function __construct()
    {
        $this->constants();
        $this->env();
        $this->session();
        $this->autoload();
    }

    private function constants(): void
    {
        define('BASE_PATH', dirname(__DIR__));
    }

    private function env(): void
    {
        $file = BASE_PATH . '/.env';
        if (file_exists($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
                [$k, $v] = explode('=', $line, 2);
                $_ENV[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
            }
        }

        define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
        define('APP_NAME', $_ENV['APP_NAME'] ?? 'Transcript System');
        define('BASE_URL', rtrim($_ENV['APP_URL'] ?? '', '/'));

        if (APP_ENV === 'development') {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }
    }

    private function session(): void
    {
        ini_set('session.gc_maxlifetime', $_ENV['SESSION_LIFETIME'] ?? 7200);
        session_name($_ENV['SESSION_NAME'] ?? 'transcript_app');
        session_set_cookie_params([
            'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function autoload(): void
    {
        spl_autoload_register(function (string $cls): void {
            foreach ([
                BASE_PATH . '/core/',
                BASE_PATH . '/app/models/',
                BASE_PATH . '/app/controllers/',
                BASE_PATH . '/app/services/',
                BASE_PATH . '/app/middleware/',
            ] as $dir) {
                $f = $dir . $cls . '.php';
                if (file_exists($f)) { require_once $f; return; }
            }
        });
        require_once BASE_PATH . '/app/helpers/helpers.php';
    }

    public function run(): void
    {
        $router = new Router();
        require BASE_PATH . '/routes/web.php';
        $router->dispatch();
    }
}
