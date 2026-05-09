<?php

declare(strict_types=1);

$tituloPagina = $tituloPagina ?? 'PipelineDesk | Inicio';
$archivoCssVista = 'index.css';
$archivoJsVista = 'index.js';
$diapositivas = (isset($diapositivas) && is_array($diapositivas)) ? array_values($diapositivas) : [];

$diapositivaInicial = $diapositivas[0];

require_once APP_ROOT . '/app/views/layouts/header.php';
?>

<header class="cabecera-inicio">
    <div class="contenedor fila-cabecera">
        <a href="<?= BASE_URL ?>" class="marca" aria-label="Ir al inicio de PipelineDesk">
            <img src="<?= BASE_URL . 'img/logo-crm.png' ?>" alt="Logo de PipelineDesk">
            <span>PipelineDesk</span>
        </a>

        <nav class="acciones" aria-label="Navegación principal">
            <a href="<?= BASE_URL ?>login" class="boton boton-secundario">Iniciar sesión</a>
            <a href="<?= BASE_URL ?>contacto" class="boton boton-primario" aria-label="Ir al formulario de Contacto">
                Formulario lead
            </a>
        </nav>
    </div>
</header>

<section class="eslogan">
    <div class="contenedor">
        <p>
            PipelineDesk: organiza tu embudo de ventas, mejora el seguimiento comercial.
        </p>
    </div>
</section>

<main class="inicio">
    <section class="contenedor hero">
        <p class="etiqueta">PipelineDesk</p>
        <h1>Comienza a gestionar tu CRM</h1>
        <p class="intro">
            Una plataforma centrada en leads, seguimiento comercial, evolución del embudo y visualización clara del trabajo.
        </p>

        <section
            id="visorPresentacion"
            class="visor"
            aria-label="Presentación visual del proyecto"
            data-diapositivas='<?= htmlspecialchars(json_encode($diapositivas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'>
            <div class="diapositiva" id="diapositivaActiva" tabindex="0" aria-live="polite">
                <img
                    src="<?= htmlspecialchars($diapositivaInicial['imagen'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($diapositivaInicial['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                    id="imagenDiapositiva">

                <div class="texto-diapositiva">
                    <h2 id="tituloDiapositiva"><?= htmlspecialchars($diapositivaInicial['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p id="textoDiapositiva"><?= htmlspecialchars($diapositivaInicial['texto'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>

            <div class="controles">
                <button type="button" class="boton-control" id="botonAnterior" aria-label="Ver diapositiva anterior">
                    Anterior
                </button>

                <p class="contador">
                    <span id="contadorDiapositiva">1</span> / <span id="totalDiapositivas"><?= count($diapositivas) ?></span>
                </p>

                <button type="button" class="boton-control" id="botonSiguiente" aria-label="Ver siguiente diapositiva">
                    Siguiente
                </button>
            </div>

            <p class="ayuda">
                Haz clic sobre la diapositiva o usa los botones para avanzar durante la presentación.
            </p>
        </section>
    </section>
</main>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
