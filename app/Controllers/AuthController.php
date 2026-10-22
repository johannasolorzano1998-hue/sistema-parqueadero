<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\User;

final class AuthController
{
    public function showLogin(): void
    {
        if (current_user()) {
            redirect('/');
        }

        View::render('auth/login', [
            'error' => flash('error'),
        ], 'layouts/auth');
    }

    public function login(): void
    {
        verify_csrf();

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $user = (new User())->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            flash('error', 'Las credenciales no son correctas.');
            redirect('/login');
        }

        login_user($user);
        redirect('/');
    }

    public function logout(): void
    {
        verify_csrf();
        logout_user();
        redirect('/login');
    }
}
