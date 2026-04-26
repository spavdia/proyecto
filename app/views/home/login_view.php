<?php
declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Iniciar sesión';
$archivoCssVista = 'login.css';
$archivoJsVista = null;

require_once APP_ROOT . '/app/views/layouts/header.php';
?>

<header class="cabecera-login">
    <div class="contenedor">
        <a href="<?= BASE_URL ?>" class="marca" aria-label="Ir al inicio de PipelineDesk">
            <img src="<?= BASE_URL . 'img/logo-crm.png' ?>" alt="Logo de PipelineDesk">
            <span>PipelineDesk</span>
        </a>
    </div>
</header>

<main class="login">
    <section class="contenedor">
        <div class="tarjeta-login" aria-labelledby="tituloLogin">
            <div class="cabecera-formulario">
                <h1 id="tituloLogin">Inicia sesión</h1>
                <p>
                    Introduce tus credenciales para acceder a PipelineDesk.
                </p>
            </div>
            <!-- Mensaje Flash-->
            <?php if (!empty($mensajeFlash)): ?>
                <div class="mensaje-flash mensaje-<?= htmlspecialchars($claseFlash ?? 'error') ?>" role="alert" aria-live="assertive">
                    <?php if (!empty($iconoFlash)): ?>
                        <span class="icono-flash" aria-hidden="true"><?= htmlspecialchars($iconoFlash) ?></span>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($mensajeFlash) ?></span>
                </div>
            <?php endif; ?>
            <!-- Error general-->
            <?php if (!empty($errores['general'])): ?>
                <div class="mensaje-flash mensaje-error" role="alert" aria-live="assertive">
                    <span><?= htmlspecialchars($errores['general']) ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulario Login-->
            <form class="formulario-login" action="<?= BASE_URL . 'login' ?>" method="POST" novalidate>
                <div class="grupo-campo">
                    <label for="email">Correo electrónico de trabajo</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nombre@crm.com"
                        value="<?= htmlspecialchars($email ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                    <span class="error-campo">
                        <?= !empty($errores['email']) ? htmlspecialchars($errores['email']) : '' ?>
                    </span>
                </div>

                <div class="grupo-campo">
                    <label for="password">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Introduce tu contraseña"
                        required
                        autocomplete="current-password"
                    >
                    <span class="error-campo">
                        <?= !empty($errores['password']) ? htmlspecialchars($errores['password']) : '' ?>
                    </span>
                </div>

                <button type="submit" class="boton boton-primario boton-login">
                    Iniciar sesión
                </button>
            </form>

            <div class="pie-login">
                <p>
                    Si tienes problemas de acceso, consulta con el administrador.
                </p>
                <p>
                    <a href="<?= BASE_URL ?>" class="enlace-simple">Volver al inicio</a>
                </p>
            </div>
        </div>
    </section>
</main>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>