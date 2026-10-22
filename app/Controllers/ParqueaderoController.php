<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Registro;
use App\Models\User;
use PDOException;

final class ParqueaderoController
{
    private const CAPACIDAD_TOTAL = 40;

    public function index(): void
    {
        $user = require_role([User::ROLE_ADMIN, User::ROLE_OPERATOR]);
        $registroModel = new Registro();

        $vehiculosDentro = $registroModel->activos();
        $carrosDentro = count(array_filter($vehiculosDentro, fn (array $vehiculo): bool => $vehiculo['tipo_vehiculo'] === 'Carro'));
        $motosDentro = count(array_filter($vehiculosDentro, fn (array $vehiculo): bool => $vehiculo['tipo_vehiculo'] === 'Moto'));
        $ocupacion = min(100, (int) round((count($vehiculosDentro) / self::CAPACIDAD_TOTAL) * 100));
        $today = date('Y-m-d');

        $usuarios = is_admin($user) ? (new User())->allPublic() : [];
// 
        View::render('parqueadero/dashboard', [
            'user' => $user,
            'admin' => is_admin($user),
            'status' => flash('status'),
            'error' => flash('error'),
            'vehiculosDentro' => $vehiculosDentro,
            'usuarios' => $usuarios,
            'gananciasHoy' => $registroModel->totalRecaudadoPorDia($today),
            'carrosDentro' => $carrosDentro,
            'motosDentro' => $motosDentro,
            'salidasHoy' => $registroModel->salidasPorDia($today),
            'capacidadTotal' => self::CAPACIDAD_TOTAL,
            'ocupacion' => $ocupacion,
            'actividadReciente' => $registroModel->recientes(),
        ], 'layouts/app');
    }

    public function registrarEntrada(): void
    {
        require_role([User::ROLE_ADMIN, User::ROLE_OPERATOR]);
        verify_csrf();

        $placa = normalize_plate((string) ($_POST['placa'] ?? ''));
        $tipo = (string) ($_POST['tipo_vehiculo'] ?? '');

        if (! valid_plate($placa)) {
            flash('error', 'La placa debe tener el formato ABC-123 o ABC-1234.');
            redirect('/');
        }

        if (! in_array($tipo, ['Carro', 'Moto'], true)) {
            flash('error', 'Selecciona un tipo de vehiculo valido.');
            redirect('/');
        }

        $registroModel = new Registro();

        if ($registroModel->existeActivoPorPlaca($placa)) {
            flash('error', 'Este vehiculo ya se encuentra dentro del parqueadero.');
            redirect('/');
        }

        try {
            $registroModel->crearEntrada($placa, $tipo);
            flash('status', 'Entrada registrada correctamente.');
        } catch (PDOException) {
            flash('error', 'No se pudo registrar la entrada. Revisa la conexion con MySQL.');
        }

        redirect('/');
    }

    public function registrarSalida(int $id): void
    {
        require_role([User::ROLE_ADMIN, User::ROLE_OPERATOR]);
        verify_csrf();

        $registroModel = new Registro();
        $registro = $registroModel->buscarActivoPorId($id);

        if (! $registro) {
            flash('error', 'No se encontro el registro de entrada o ya fue dado de salida.');
            redirect('/');
        }

        $totalPagar = $registroModel->registrarSalida($registro);
        flash('status', 'Salida registrada. Total a pagar: $' . number_format($totalPagar, 2));
        redirect('/');
    }
}
