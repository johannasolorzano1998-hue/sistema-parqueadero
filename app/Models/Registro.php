<?php

declare(strict_types=1);

namespace App\Models;

use Database;
use PDO;

final class Registro
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function activos(): array
    {
        return $this->pdo
            ->query("SELECT * FROM registros WHERE estado = 'dentro' ORDER BY hora_entrada DESC")
            ->fetchAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recientes(int $limit = 5): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM registros ORDER BY updated_at DESC, id DESC LIMIT :limit');
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function totalRecaudadoPorDia(string $date): float
    {
        $statement = $this->pdo->prepare(
            "SELECT COALESCE(SUM(total_pagar), 0)
             FROM registros
             WHERE estado = 'salido' AND DATE(hora_salida) = :date"
        );
        $statement->execute(['date' => $date]);

        return (float) $statement->fetchColumn();
    }

    public function salidasPorDia(string $date): int
    {
        $statement = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM registros
             WHERE estado = 'salido' AND DATE(hora_salida) = :date"
        );
        $statement->execute(['date' => $date]);

        return (int) $statement->fetchColumn();
    }

    public function existeActivoPorPlaca(string $placa): bool
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM registros WHERE placa = :placa AND estado = :estado');
        $statement->execute(['placa' => $placa, 'estado' => 'dentro']);

        return (int) $statement->fetchColumn() > 0;
    }

    public function crearEntrada(string $placa, string $tipoVehiculo): void
    {
        $now = date('Y-m-d H:i:s');
        $statement = $this->pdo->prepare(
            'INSERT INTO registros
                (placa, tipo_vehiculo, hora_entrada, estado, total_pagar, created_at, updated_at)
             VALUES
                (:placa, :tipo_vehiculo, :hora_entrada, :estado, :total_pagar, :created_at, :updated_at)'
        );

        $statement->execute([
            'placa' => $placa,
            'tipo_vehiculo' => $tipoVehiculo,
            'hora_entrada' => $now,
            'estado' => 'dentro',
            'total_pagar' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function buscarActivoPorId(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM registros WHERE id = :id AND estado = :estado LIMIT 1');
        $statement->execute(['id' => $id, 'estado' => 'dentro']);

        $registro = $statement->fetch();

        return $registro ?: null;
    }

    public function registrarSalida(array $registro): float
    {
        $horaSalida = date('Y-m-d H:i:s');
        $minutos = max(1, (int) ceil((strtotime($horaSalida) - strtotime($registro['hora_entrada'])) / 60));
        $horas = max(1, (int) ceil($minutos / 60));
        $tarifa = $registro['tipo_vehiculo'] === 'Carro' ? 2 : 1;
        $totalPagar = (float) ($horas * $tarifa);

        $statement = $this->pdo->prepare(
            'UPDATE registros
             SET hora_salida = :hora_salida, total_pagar = :total_pagar, estado = :estado, updated_at = :updated_at
             WHERE id = :id'
        );
        $statement->execute([
            'hora_salida' => $horaSalida,
            'total_pagar' => $totalPagar,
            'estado' => 'salido',
            'updated_at' => $horaSalida,
            'id' => (int) $registro['id'],
        ]);

        return $totalPagar;
    }
}
