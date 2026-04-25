<?php
declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Iniciar sesión';
$archivoCssVista = 'login.css';
$archivoJsVista = null;

require_once APP_ROOT . '/app/views/layouts/header.php';

?>

<header class="encabezado-login">
    <div class="contenedor encabezado-login-contenido">
        <a href="<?= BASE_URL ?>" class="marca" aria-label="Ir al inicio de PipelineDesk">
            <img src="<?= BASE_URL . 'img/logo-crm.png' ?>" alt="Logo de PipelineDesk" class="marca-logo">
            <span class="marca-texto">PipelineDesk</span>
        </a>
    </div>
</header>

<main class="zona-login">
    <section class="contenedor">
        <div class="tarjeta-login" aria-labelledby="tituloLogin">
            <div class="cabecera-login">
                <h1 id="tituloLogin" class="titulo-login">Inicia sesión</h1>
                <p class="texto-login">
                    Introduce tus credenciales para acceder a PipelineDesk.
                </p>
            </div>

            <?php if (!empty($mensajeFlash)): ?>
                <div class="mensaje-flash mensaje-<?= htmlspecialchars($claseFlash ?? 'error') ?>" role="alert" aria-live="assertive">
                    <?php if (!empty($iconoFlash)): ?>
                        <span class="mensaje-icono" aria-hidden="true"><?= htmlspecialchars($iconoFlash) ?></span>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($mensajeFlash) ?></span>
                </div>
            <?php endif; ?>

            <form class="formulario-login" action="<?= BASE_URL . 'login' ?>" method="POST" novalidate>
                <div class="grupo-campo">
                    <label for="email" class="etiqueta-campo">Correo electrónico de tu empresa</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="campo-texto"
                        placeholder="ejemplo@empresa.com"
                        value="<?= htmlspecialchars($emailAnterior ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="grupo-campo">
                    <label for="password" class="etiqueta-campo">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="campo-texto"
                        placeholder="Introduce tu contraseña"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="boton boton-primario boton-login">
                    Iniciar sesión
                </button>
            </form>

            <div class="pie-login">
                <p class="texto-secundario-login">
                    Acceso interno al CRM Pipeline. Si tienes problemas de acceso, revisa tu usuario o consulta con el administrador.
                </p>
                <p class="volver-inicio">
                    <a href="<?= BASE_URL ?>" class="enlace-secundario">Volver al inicio</a>
                </p>
            </div>
        </div>
    </section>
</main>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>