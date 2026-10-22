<main class="login-shell">
    <section class="login-card">
        <div class="login-brand">
            <div class="brand-mark">SP</div>
            <div>
                <h1>Sistema Parqueadero</h1>
                <p class="muted">Operacion, control y cobro en un solo panel.</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login" class="form-stack">
            <?= csrf_field() ?>
            <label>
                Correo
                <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
            </label>
            <label>
                Contrasena
                <input type="password" name="password" required>
            </label>
            <button type="submit">
                <span class="button-icon">&gt;</span>
                Ingresar
            </button>
        </form>

        <div class="demo-box">
            <strong>Demo</strong>
            <span>admin@example.com / Admin123!</span>
            <span>operador@example.com / Operador123!</span>
        </div>
    </section>
</main>
