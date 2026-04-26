<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Contacto';
$archivoCssVista = 'contacto.css';
$archivoJsVista = null;

require_once APP_ROOT . '/app/views/layouts/header.php';

$datosAntiguos = $datosAntiguos ?? [];
$serviciosLista = $serviciosLista ?? [];
?>

<header class="cabecera-contacto">
    <div class="contenedor fila-cabecera">
        <a href="<?= BASE_URL ?>" class="marca" aria-label="Ir al inicio de PipelineDesk">
            <img src="<?= BASE_URL . 'img/logo-crm.png' ?>" alt="Logo de PipelineDesk">
            <span>PipelineDesk</span>
        </a>

        <nav class="acciones">
            <a href="<?= BASE_URL ?>" class="boton boton-secundario">Inicio</a>
            <a href="<?= BASE_URL . 'login' ?>" class="boton boton-primario">Iniciar sesión</a>
        </nav>
    </div>
</header>

<main class="contacto">
    <section class="contenedor contacto-grid">
        <div class="info">
            <h1>Solicita información</h1>
            <p class="intro">
                Envía el formulario para que una persona de nuestro equipo comience a ayudarte de forma personalizada.
            </p>

            <ul class="datos">
                <li><strong>Centro:</strong> Tu negocio</li>
                <li><strong>Teléfono:</strong> 955 123 123</li>
                <li><strong>Email:</strong> contacto@tunegocio.com</li>
            </ul>

            <p class="nota">
                Estás a solo un click para comenzar a conseguir tus objetivos.
            </p>
        </div>

        <div class="formulario-box">
            <?php
            $errores = $errores ?? [];
            ?>

            <?php if (!empty($mensajeFlash)): ?>
                <div class="mensaje-flash mensaje-<?= htmlspecialchars($claseFlash ?? 'info') ?>" role="alert" aria-live="assertive">
                    <?php if (!empty($iconoFlash)): ?>
                        <span class="icono-flash" aria-hidden="true"><?= htmlspecialchars($iconoFlash) ?></span>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($mensajeFlash) ?></span>
                </div>
            <?php endif; ?>

            <form class="formulario" action="<?= BASE_URL . 'contacto' ?>" method="POST" novalidate>
                <div class="campo">
                    <label for="lead_nombre">Nombre completo</label>
                    <input
                        type="text"
                        id="lead_nombre"
                        name="lead_nombre"
                        placeholder="Tu nombre"
                        value="<?= htmlspecialchars($lead_nombre ?? '') ?>"
                        required
                        autocomplete="name">
                    <span class="error-campo">
                        <?= !empty($errores['lead_nombre']) ? htmlspecialchars($errores['lead_nombre']) : '' ?>
                    </span>
                </div>

                <div class="campo">
                    <label for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="tuemail@correo.com"
                        value="<?= htmlspecialchars($email ?? '') ?>"
                        autocomplete="email">
                    <span class="error-campo">
                        <?= !empty($errores['email']) ? htmlspecialchars($errores['email']) : '' ?>
                    </span>
                </div>

                <div class="campo">
                    <label for="telefono">Teléfono</label>
                    <input
                        type="text"
                        id="telefono"
                        name="telefono"
                        placeholder="Tu teléfono"
                        value="<?= htmlspecialchars($telefono ?? '') ?>"
                        autocomplete="tel">
                    <span class="error-campo">
                        <?= !empty($errores['telefono']) ? htmlspecialchars($errores['telefono']) : '' ?>
                    </span>
                </div>

                <div class="campo">
                    <label for="servicios">Servicio de interés</label>
                    <select id="servicios" name="servicios" required>
                        <option value="">Selecciona un servicio</option>
                        <?php foreach ($serviciosLista as $servicio): ?>
                            <option
                                value="<?= htmlspecialchars($servicio) ?>"
                                <?= (($servicios ?? '') === $servicio) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($servicio) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-campo">
                        <?= !empty($errores['servicios']) ? htmlspecialchars($errores['servicios']) : '' ?>
                    </span>
                </div>

                <div class="campo">
                    <label for="indicaciones">Indicaciones</label>
                    <textarea
                        id="indicaciones"
                        name="indicaciones"
                        rows="5"
                        placeholder="Cuéntanos brevemente qué necesitas"><?= htmlspecialchars($indicaciones ?? '') ?></textarea>
                    <span class="error-campo">
                        <?= !empty($errores['indicaciones']) ? htmlspecialchars($errores['indicaciones']) : '' ?>
                    </span>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="boton boton-enviar">Enviar solicitud</button>
                </div>
            </form>


        </div>
    </section>
</main>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>