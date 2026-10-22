<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Parqueadero</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">SP</div>
            <div>
                <strong>SmartPark</strong>
                <span>Sistema parqueadero</span>
            </div>
        </div>

        <nav class="nav">
            <a href="#dashboard" class="nav-link active">
                <span class="nav-icon">D</span>
                Dashboard
            </a>
            <a href="#patio" class="nav-link">
                <span class="nav-icon">P</span>
                Patio
            </a>
            <?php if ($admin): ?>
                <a href="#usuarios" class="nav-link">
                    <span class="nav-icon">U</span>
                    Usuarios
                </a>
            <?php endif; ?>
        </nav>

        <div class="session-box">
            <span><?= e($admin ? 'Administrador' : 'Operador') ?></span>
            <strong><?= e($user['name']) ?></strong>
            <form method="POST" action="/logout">
                <?= csrf_field() ?>
                <button type="submit" class="button ghost">
                    <span class="button-icon">x</span>
                    Salir
                </button>
            </form>
        </div>
    </aside>

    <main class="content">
        <header class="topbar">
            <div>
                <span class="eyebrow">Panel operativo</span>
                <h1>Estadisticas y monitoreo</h1>
                <p>Terminal Terrestre - Quito &middot; <?= e(date('d/m/Y H:i')) ?></p>
            </div>
            <div class="topbar-status">
                <span class="status-dot"></span>
                Sistema activo
            </div>
        </header>

        <?= $content ?>
    </main>
</body>
</html>
