<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Panel';
$archivoCssVista = 'panel.css';
$archivoJsVista = 'panel.js';
$menuActivo = 'panel';

require_once APP_ROOT . '/app/views/layouts/header.php';

$usuario = (isset($usuario) && is_array($usuario)) ? $usuario : [];
$leadsPorEstado = (isset($leadsPorEstado) && is_array($leadsPorEstado)) ? $leadsPorEstado : [];
$estadosLista = (isset($estadosLista) && is_array($estadosLista)) ? $estadosLista : [];
$tareasRetrasadasCount = (int) ($tareasRetrasadasCount ?? 0);

$nombreUsuario = (string) ($usuario['nombre'] ?? 'Usuario');
$rolUsuario = (string) ($usuario['rol'] ?? 'ventas');

$estadosPanel = [
    'Nuevo Lead' => [
        'titulo' => 'Nuevos',
        'clase'  => 'estado-nuevo',
        'texto'  => 'Pendientes de contacto'
    ],
    'Contactado' => [
        'titulo' => 'Contactados',
        'clase'  => 'estado-contactado',
        'texto'  => 'Seguimiento activo'
    ],
    'En Progreso' => [
        'titulo' => 'En progreso',
        'clase'  => 'estado-progreso',
        'texto'  => 'Oportunidades en desarrollo'
    ],
    'Objeciones' => [
        'titulo' => 'Objeciones',
        'clase'  => 'estado-objeciones',
        'texto'  => 'Bloqueos por resolver'
    ],
    'Ganado' => [
        'titulo' => 'Ganados',
        'clase'  => 'estado-ganado',
        'texto'  => 'Operaciones cerradas'
    ],
    'Perdido' => [
        'titulo' => 'Perdidos',
        'clase'  => 'estado-perdido',
        'texto'  => 'Oportunidades descartadas'
    ]
];

$clasesServicios = [
    'B1 Inglés' => 'servicio-b1',
    'B2 Inglés' => 'servicio-b2',
    'Informática' => 'servicio-informatica',
    'Apoyo Primaria' => 'servicio-primaria',
    'Apoyo Secundaria' => 'servicio-secundaria',
    'Apoyo Bach' => 'servicio-bach',
    'Apoyo Univ' => 'servicio-univ',
    'Acceso a GS' => 'servicio-gs',
    'Selectividad' => 'servicio-selectividad',
    'Acceso Univ+25' => 'servicio-univ25'
];

$mostrarEnlaceTareasFlash = !empty($mensajeFlash)
    && (string) ($claseFlash ?? 'info') === 'info'
    && mb_stripos((string) $mensajeFlash, 'objeción pendiente') !== false;
?>

<div class="panel" id="panelApp">
    <?php require APP_ROOT . '/app/views/layouts/panel_aside.php'; ?>

    <div class="fondo-menu" id="fondoMenu" aria-hidden="true"></div>

    <main class="contenido">
        <header class="cabecera-panel">
            <div class="cabecera-info">
                <p class="cabecera-etiqueta">CRM Pipeline</p>
                <h1>Vista general del embudo comercial</h1>
                <p class="cabecera-texto">
                    Panel principal de PipelineDesk con los leads agrupados por estado del embudo.
                </p>
            </div>

            <div class="cabecera-acciones">
                <button
                    type="button"
                    class="boton-menu"
                    id="botonMenu"
                    aria-controls="asidePanel"
                    aria-expanded="false"
                    aria-label="Abrir menú">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <div class="usuario">
                    <span class="usuario-nombre"><?= htmlspecialchars($nombreUsuario) ?></span>
                    <span class="usuario-rol"><?= htmlspecialchars($rolUsuario) ?></span>
                </div>
            </div>
        </header>

        <?php if (!empty($tareasRetrasadasCount)): ?>
            <div class="mensaje-flash mensaje-error" role="alert" aria-live="assertive">
                <span class="icono-flash" aria-hidden="true">⏰</span>
                <span>
                    Tienes <?= (int) $tareasRetrasadasCount ?> tarea<?= ((int) $tareasRetrasadasCount === 1) ? '' : 's' ?> retrasada<?= ((int) $tareasRetrasadasCount === 1) ? '' : 's' ?>.
                    <a href="<?= BASE_URL . 'tareas' ?>" style="font-weight:700; color:inherit; text-decoration:underline;">Ver tareas</a>
                </span>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensajeFlash)): ?>
            <div class="mensaje-flash mensaje-<?= htmlspecialchars((string) ($claseFlash ?? 'info')) ?>" role="alert" aria-live="assertive">
                <?php if (!empty($iconoFlash)): ?>
                    <span class="icono-flash" aria-hidden="true"><?= htmlspecialchars((string) $iconoFlash) ?></span>
                <?php endif; ?>

                <span>
                    <?= htmlspecialchars((string) $mensajeFlash) ?>
                    <?php if ($mostrarEnlaceTareasFlash): ?>
                        <a href="<?= BASE_URL . 'tareas' ?>" style="font-weight:700; color:inherit; text-decoration:underline;"> Ver tareas</a>
                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>

        <?php foreach ($estadosPanel as $estadoClave => $configEstado): ?>
            <?php $leadsEstado = $leadsPorEstado[$estadoClave] ?? []; ?>

            <section class="bloque">
                <div class="bloque-top">
                    <h2><?= htmlspecialchars($configEstado['titulo']) ?></h2>
                    <span class="estado <?= htmlspecialchars($configEstado['clase']) ?>">
                        <?= htmlspecialchars($configEstado['texto']) ?>
                    </span>
                </div>

                <div class="tabla-wrap">
                    <table class="tabla-panel">
                        <thead>
                            <tr>
                                <th>Lead</th>
                                <th>Responsable</th>
                                <th>Estado</th>
                                <th>Servicio</th>
                                <th>Teléfono</th>
                                <th>Indicaciones</th>
                                <th>Último contacto</th>
                                <th>Origen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leadsEstado)): ?>
                                <tr>
                                    <td colspan="8" class="tabla-vacia">No hay leads en este estado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($leadsEstado as $lead): ?>
                                    <?php
                                    $estadoActual = (string) ($lead['estado'] ?? '');
                                    $claseEstado = $estadosPanel[$estadoActual]['clase'] ?? '';
                                    $servicioActual = (string) ($lead['servicios'] ?? '');
                                    $claseServicio = $clasesServicios[$servicioActual] ?? 'servicio-general';
                                    ?>
                                    <tr>
                                        <td class="celda-lead <?= htmlspecialchars($claseEstado) ?>">
                                            <a href="<?= BASE_URL . 'leads/' . (int) ($lead['id'] ?? 0) ?>" class="enlace-lead">
                                                <?= htmlspecialchars((string) ($lead['lead_nombre'] ?? '')) ?>
                                            </a>
                                        </td>

                                        <td><?= htmlspecialchars((string) ($lead['responsable_nombre'] ?? 'Sin asignar')) ?></td>

                                        <td>
                                            <form
                                                action="<?= BASE_URL . 'leads/cambiar-estado/' . (int) ($lead['id'] ?? 0) ?>"
                                                method="POST"
                                                class="form-estado">
                                                <div class="estado-campo">
                                                    <select
                                                        name="estado"
                                                        class="selector-estado <?= htmlspecialchars($claseEstado) ?>"
                                                        aria-label="Cambiar estado del lead <?= htmlspecialchars((string) ($lead['lead_nombre'] ?? '')) ?>">
                                                        <?php foreach ($estadosLista as $estadoItem): ?>
                                                            <?php $estadoItemTexto = (string) $estadoItem; ?>
                                                            <option
                                                                value="<?= htmlspecialchars($estadoItemTexto) ?>"
                                                                <?= ($estadoActual === $estadoItemTexto) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($estadoItemTexto) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </form>
                                        </td>

                                        <td>
                                            <span class="etiqueta-servicio <?= htmlspecialchars($claseServicio) ?>">
                                                <?= htmlspecialchars($servicioActual) ?>
                                            </span>
                                        </td>

                                        <td><?= htmlspecialchars((string) ($lead['telefono'] ?? '-')) ?></td>

                                        <td class="texto-indicaciones">
                                            <?= !empty($lead['indicaciones'])
                                                ? htmlspecialchars(mb_strimwidth((string) $lead['indicaciones'], 0, 70, '...'))
                                                : '-' ?>
                                        </td>

                                        <td>
                                            <?= !empty($lead['ultimo_contacto'])
                                                ? htmlspecialchars((string) ($lead['ultimo_contacto']))
                                                : 'Sin contacto'; ?>
                                        </td>

                                        <td>
                                            <span class="etiqueta-origen">
                                                <?= (($lead['origen'] ?? '') === 'formulario_web') ? 'Web' : 'Interna' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endforeach; ?>
    </main>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>