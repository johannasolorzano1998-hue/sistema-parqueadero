<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ParqueaderoController;

require_once dirname(__DIR__) . '/src/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();
$parqueaderoController = new ParqueaderoController();

if ($path === '/login' && $method === 'GET') {
    $authController->showLogin();
    exit;
}

if ($path === '/login' && $method === 'POST') {
    $authController->login();
    exit;
}

if ($path === '/logout' && $method === 'POST') {
    $authController->logout();
    exit;
}

if ($path === '/' && $method === 'GET') {
    $parqueaderoController->index();
    exit;
}

if ($path === '/entrada' && $method === 'POST') {
    $parqueaderoController->registrarEntrada();
    exit;
}

if (preg_match('#^/salida/([0-9]+)$#', $path, $matches) && $method === 'POST') {
    $parqueaderoController->registrarSalida((int) $matches[1]);
    exit;
}

http_response_code(404);
echo 'Pagina no encontrada.';
