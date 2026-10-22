<?php

declare(strict_types=1);

final class Database
{
    public static function connection(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $config = self::config();
        $pdo = $config['DB_CONNECTION'] === 'mysql'
            ? self::mysql($config)
            : self::sqlite();

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        self::migrate($pdo, $config['DB_CONNECTION']);
        self::seed($pdo);

        return $pdo;
    }

    private static function config(): array
    {
        $config = [
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => '127.0.0.1',
            'DB_PORT' => '3306',
            'DB_DATABASE' => 'sistema_parqueadero',
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '',
        ];

        $envPath = dirname(__DIR__) . '/.env';
        if (! file_exists($envPath)) {
            return $config;
        }

        foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $config[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }

        return $config;
    }

    private static function mysql(array $config): PDO
    {
        $database = $config['DB_DATABASE'];
        if (! preg_match('/^[A-Za-z0-9_]+$/', $database)) {
            throw new RuntimeException('Nombre de base de datos invalido.');
        }

        $serverDsn = sprintf(
            'mysql:host=%s;port=%s;charset=utf8mb4',
            $config['DB_HOST'],
            $config['DB_PORT']
        );

        $server = new PDO($serverDsn, $config['DB_USERNAME'], $config['DB_PASSWORD']);
        $server->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $server->exec(
            "CREATE DATABASE IF NOT EXISTS `$database`
             CHARACTER SET utf8mb4
             COLLATE utf8mb4_unicode_ci"
        );

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['DB_HOST'],
            $config['DB_PORT'],
            $database
        );

        return new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD']);
    }

    private static function sqlite(): PDO
    {
        $path = dirname(__DIR__) . '/database/parqueadero.sqlite';

        return new PDO('sqlite:' . $path);
    }

    private static function migrate(PDO $pdo, string $connection): void
    {
        if ($connection === 'mysql') {
            self::migrateMysql($pdo);
            return;
        }

        self::migrateSqlite($pdo);
    }

    private static function migrateSqlite(PDO $pdo): void
    {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'operador',
                created_at TEXT NOT NULL
            )"
        );
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS registros (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                placa TEXT NOT NULL,
                tipo_vehiculo TEXT NOT NULL CHECK (tipo_vehiculo IN ('Carro', 'Moto')),
                hora_entrada TEXT NOT NULL,
                hora_salida TEXT NULL,
                total_pagar REAL NOT NULL DEFAULT 0,
                estado TEXT NOT NULL DEFAULT 'dentro' CHECK (estado IN ('dentro', 'salido')),
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )"
        );

        $pdo->exec(
            "CREATE UNIQUE INDEX IF NOT EXISTS registros_placa_dentro_unique
                ON registros (placa)
                WHERE estado = 'dentro'"
        );
    }

    private static function migrateMysql(PDO $pdo): void
    {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(160) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) NOT NULL DEFAULT 'operador',
                created_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS registros (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                placa VARCHAR(8) NOT NULL,
                tipo_vehiculo ENUM('Carro', 'Moto') NOT NULL,
                hora_entrada DATETIME NOT NULL,
                hora_salida DATETIME NULL,
                total_pagar DECIMAL(8, 2) NOT NULL DEFAULT 0,
                estado ENUM('dentro', 'salido') NOT NULL DEFAULT 'dentro',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX registros_estado_placa_index (estado, placa),
                INDEX registros_hora_salida_index (hora_salida)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        self::ensureMysqlColumn($pdo, 'users', 'role', "role VARCHAR(20) NOT NULL DEFAULT 'operador'");
        self::ensureMysqlColumn($pdo, 'users', 'created_at', 'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    private static function ensureMysqlColumn(PDO $pdo, string $table, string $column, string $definition): void
    {
        $allowedDefinitions = [
            'users.role' => "role VARCHAR(20) NOT NULL DEFAULT 'operador'",
            'users.created_at' => 'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
        ];
        $key = $table . '.' . $column;

        if (! isset($allowedDefinitions[$key]) || $allowedDefinitions[$key] !== $definition) {
            throw new RuntimeException('Definicion de columna no permitida.');
        }

        $rawTable = $table;
        $quotedTable = self::quoteIdentifier($table);

        $statement = $pdo->prepare(
            "SELECT COUNT(*)
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = :table_name
                AND COLUMN_NAME = :column_name"
        );
        $statement->execute([
            'table_name' => $rawTable,
            'column_name' => $column,
        ]);

        if ((int) $statement->fetchColumn() > 0) {
            return;
        }

        $pdo->exec("ALTER TABLE $quotedTable ADD COLUMN $definition");
    }

    private static function quoteIdentifier(string $identifier): string
    {
        if (! preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
            throw new RuntimeException('Identificador SQL invalido.');
        }

        return '`' . $identifier . '`';
    }

    private static function seed(PDO $pdo): void
    {
        $statement = $pdo->prepare(
            'INSERT INTO users (name, email, password, role, created_at)
             VALUES (:name, :email, :password, :role, :created_at)'
        );
        $exists = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');

        $now = date('Y-m-d H:i:s');
        $users = [
            ['Administrador', 'admin@example.com', 'Admin123!', 'admin'],
            ['Operador', 'operador@example.com', 'Operador123!', 'operador'],
        ];

        foreach ($users as [$name, $email, $password, $role]) {
            $exists->execute(['email' => $email]);
            if ((int) $exists->fetchColumn() > 0) {
                continue;
            }

            $statement->execute([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'created_at' => $now,
            ]);
        }
    }
}
