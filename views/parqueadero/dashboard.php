<?php if ($status): ?>
    <div class="alert alert-success"><?= e($status) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<section id="dashboard" class="hero-dashboard">
    <div>
        <span class="eyebrow">Ocupacion actual</span>
        <h2><?= count($vehiculosDentro) ?> de <?= $capacidadTotal ?> espacios ocupados</h2>
        <p>Distribucion activa: <?= $carrosDentro ?> carros y <?= $motosDentro ?> motos dentro del parqueadero.</p>
    </div>
    <div class="occupancy">
        <strong><?= $ocupacion ?>%</strong>
        <span>ocupado</span>
        <div class="progress">
            <div style="width: <?= $ocupacion ?>%"></div>
        </div>
    </div>
</section>

<section class="stats-grid">
    <article class="stat-card stat-blue">
        <div class="stat-icon">P</div>
        <span>Vehiculos activos</span>
        <strong><?= count($vehiculosDentro) ?></strong>
    </article>
    <article class="stat-card stat-green">
        <div class="stat-icon">$</div>
        <span>Recaudacion de hoy</span>
        <strong>$<?= number_format($gananciasHoy, 2) ?></strong>
    </article>
    <article class="stat-card stat-amber">
        <div class="stat-icon">S</div>
        <span>Salidas registradas</span>
        <strong><?= $salidasHoy ?></strong>
    </article>
    <?php if ($admin): ?>
        <article class="stat-card stat-violet">
            <div class="stat-icon">U</div>
            <span>Cuentas del sistema</span>
            <strong><?= count($usuarios) ?></strong>
        </article>
    <?php endif; ?>
</section>

<section id="patio" class="work-grid">
    <article class="panel">
        <div class="panel-header compact">
            <div>
                <span class="eyebrow">Nuevo registro</span>
                <h2>Registrar ingreso</h2>
            </div>
        </div>
        <form method="POST" action="/entrada" class="form-stack">
            <?= csrf_field() ?>
            <label>
                Placa vehicular
                <input
                    type="text"
                    name="placa"
                    placeholder="PBW-1234"
                    maxlength="8"
                    pattern="[A-Za-z]{3}-?[0-9]{3,4}"
                    required
                >
            </label>
            <label>
                Tipo
                <select name="tipo_vehiculo" required>
                    <option value="">Seleccione...</option>
                    <option value="Carro">Carro ($2.00/h)</option>
                    <option value="Moto">Moto ($1.00/h)</option>
                </select>
            </label>
            <button type="submit">
                <span class="button-icon">+</span>
                Registrar entrada
            </button>
        </form>
    </article>

    <article class="panel panel-wide">
        <div class="panel-header">
            <h2>Vehiculos en patio</h2>
            <span class="pill"><?= count($vehiculosDentro) ?> en curso</span>
        </div>

        <?php if (! $vehiculosDentro): ?>
            <p class="empty">No hay vehiculos registrados en este momento.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Tipo</th>
                            <th>Hora entrada</th>
                            <th class="right">Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehiculosDentro as $vehiculo): ?>
                            <tr>
                                <td><span class="plate"><?= e($vehiculo['placa']) ?></span></td>
                                <td><span class="badge"><?= e($vehiculo['tipo_vehiculo']) ?></span></td>
                                <td><?= e(date('H:i d/m/Y', strtotime($vehiculo['hora_entrada']))) ?></td>
                                <td class="right">
                                    <form method="POST" action="/salida/<?= (int) $vehiculo['id'] ?>">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="button danger">
                                            <span class="button-icon">-</span>
                                            Dar salida
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>

<section class="panel">
    <div class="panel-header">
        <h2>Actividad reciente</h2>
        <span>Ultimos movimientos</span>
    </div>
    <?php if (! $actividadReciente): ?>
        <p class="empty">Aun no hay movimientos registrados.</p>
    <?php else: ?>
        <div class="activity-list">
            <?php foreach ($actividadReciente as $actividad): ?>
                <div class="activity-item">
                    <div class="activity-dot <?= e($actividad['estado']) ?>"></div>
                    <div>
                        <strong><?= e($actividad['placa']) ?></strong>
                        <span><?= e($actividad['tipo_vehiculo']) ?> &middot; <?= e($actividad['estado'] === 'dentro' ? 'Ingreso activo' : 'Salida registrada') ?></span>
                    </div>
                    <time><?= e(date('H:i d/m/Y', strtotime($actividad['updated_at']))) ?></time>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php if ($admin): ?>
    <section id="usuarios" class="panel">
        <div class="panel-header">
            <h2>Personal autorizado</h2>
            <span>Solo visible para administradores</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Creado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>#<?= (int) $usuario['id'] ?></td>
                            <td><?= e($usuario['name']) ?></td>
                            <td><?= e($usuario['email']) ?></td>
                            <td><?= e($usuario['role']) ?></td>
                            <td><?= e(date('d/m/Y H:i', strtotime($usuario['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>
