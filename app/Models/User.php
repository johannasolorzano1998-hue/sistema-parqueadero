<?php

declare(strict_types=1);

namespace App\Models;

use Database;
use PDO;

final class User
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_OPERATOR = 'operador';

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);

        $user = $statement->fetch();

        return $user ?: null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function allPublic(): array
    {
        return $this->pdo
            ->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC, id DESC')
            ->fetchAll();
    }
}
