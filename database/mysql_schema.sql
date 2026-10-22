CREATE DATABASE IF NOT EXISTS sistema_parqueadero
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sistema_parqueadero;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'operador',
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS registros (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
