<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Inicio';
$archivoCssVista = 'index.css';
$archivoJsVista = 'index.js';

require_once APP_ROOT . '/app/views/layouts/header.php';

$diapositivas = $diapositivas ?? [
    [
        'imagen' => BASE_URL . 'img/presentacion/slide-1.png',
        'titulo' => 'Bienvenido a PipelineDesk',
        'texto'  => 'CRM Pipeline diseñado para organizar clientes potenciales, tareas, seguimiento y evolución comercial.'
    ],
    [
        'imagen' => BASE_URL . 'img/presentacion/slide-2.png',
        'titulo' => 'Embudo de ventas claro y visual',
        'texto'  => 'Podrás mover leads entre estados, registrar notas, tareas y medir el rendimiento del equipo.'
    ],
    [
        'imagen' => BASE_URL . 'img/presentacion/slide-3.png',
        'titulo' => 'Presentación del proyecto',
        'texto'  => 'Haz clic sobre la diapositiva o usa los controles para avanzar durante tu exposición.'
    ]
];
?>

<header class="encabezado-principal">
    <div class="contenedor encabezado-contenido">
        <a href="<?= BASE_URL ?>" class="marca" aria-label="Ir al inicio de PipelineDesk">
            <img src="<?= BASE_URL . 'img/logo-crm.png' ?>" alt="Logo de PipelineDesk" class="marca-logo">
            <span class="marca-texto">PipelineDesk</span>
        </a>

        <nav class="navegacion-principal" aria-label="Navegación principal">
            <a href="<?= BASE_URL . 'login' ?>" class="boton boton-secundario">Iniciar sesión</a>
            <button type="button" class="boton boton-primario" aria-disabled="true" title="Disponible en fases futuras">
                Formulario lead
            </button>
        </nav>
    </div>
</header>

<section class="barra-eslogan" aria-label="Mensaje destacado">
    <div class="contenedor">
        <p class="texto-eslogan">
            PipelineDesk: organiza tu embudo de ventas, mejora el seguimiento comercial y presenta tu proyecto con claridad.
        </p>
    </div>
</section>

<main class="contenido-inicio">
    <section class="seccion-presentacion contenedor">
        <div class="cabecera-presentacion">
            <p class="etiqueta-marca">PipelineDesk CRM</p>
            <h1 class="titulo-principal">Hecho para gestionar tus clientes</h1>
            <p class="texto-principal">
                Una plataforma centrada en leads, seguimiento comercial, evolución del embudo y visualización de etapas de tu negocio.
            </p>
        </div>
        <!-- Visor de diapositivas -->
        <section
            id="visorPresentacion" class="visor-diapositivas" aria-label="Presentación visual del proyecto"
            data-diapositivas='<?= htmlspecialchars(json_encode($diapositivas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'>
            <div class="diapositiva-activa" id="diapositivaActiva" tabindex="0" aria-live="polite">
                <img
                    src="<?= htmlspecialchars($diapositivas[0]['imagen']) ?>"
                    alt="<?= htmlspecialchars($diapositivas[0]['titulo']) ?>"
                    id="imagenDiapositiva"
                    class="imagen-diapositiva">

                <div class="contenido-diapositiva">
                    <h2 id="tituloDiapositiva"><?= htmlspecialchars($diapositivas[0]['titulo']) ?></h2>
                    <p id="textoDiapositiva"><?= htmlspecialchars($diapositivas[0]['texto']) ?></p>
                </div>
            </div>

            <div class="controles-diapositivas">
                <button type="button" class="boton-control" id="botonAnterior" aria-label="Ver diapositiva anterior">
                    Anterior
                </button>

                <p class="indicador-diapositiva">
                    <span id="contadorDiapositiva">1</span> / <span id="totalDiapositivas"><?= count($diapositivas) ?></span>
                </p>

                <button type="button" class="boton-control" id="botonSiguiente" aria-label="Ver siguiente diapositiva">
                    Siguiente
                </button>
            </div>

            <p class="ayuda-diapositivas">
                Consejo: haz clic sobre la diapositiva para avanzar, o usa los botones.
            </p>
        </section>
    </section>
</main>

<script>
    window.diapositivasPipelineDesk = <?= json_encode($diapositivas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>