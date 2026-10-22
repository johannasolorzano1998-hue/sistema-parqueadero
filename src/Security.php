<?php

declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['_token'])) {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $sent = $_POST['_token'] ?? '';
    $stored = $_SESSION['_token'] ?? '';

    if (! is_string($sent) || ! hash_equals($stored, $sent)) {
        http_response_code(419);
        echo 'Token CSRF invalido.';
        exit;
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): array
{
    $user = current_user();

    if (! $user) {
        redirect('/login');
    }

    return $user;
}

/**
 * @param array<int, string> $roles
 */
function require_role(array $roles): array
{
    $user = require_auth();

    if (! in_array((string) ($user['role'] ?? ''), $roles, true)) {
        http_response_code(403);
        echo 'Acceso denegado.';
        exit;
    }

    return $user;
}

function is_admin(?array $user = null): bool
{
    $user ??= current_user();

    return ($user['role'] ?? '') === 'admin';
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function normalize_plate(string $plate): string
{
    $plate = strtoupper(trim($plate));
    $plate = preg_replace('/\s+/', '', $plate) ?? '';

    return preg_replace('/^([A-Z]{3})-?([0-9]{3,4})$/', '$1-$2', $plate) ?? $plate;
}

function valid_plate(string $plate): bool
{
    return preg_match('/^[A-Z]{3}-[0-9]{3,4}$/', $plate) === 1;
}

function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self'; img-src 'self' data:; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
}
