<?php

declare(strict_types=1);

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Security.php';

date_default_timezone_set('America/Guayaquil');
send_security_headers();

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $path = dirname(__DIR__) . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($path)) {
        require_once $path;
    }
});

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    return $message;
}
