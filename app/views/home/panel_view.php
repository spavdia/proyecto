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
$notificacionTarea = (isset($notificacionTarea) && is_array($notificacionTarea)) ? $notificacionTarea : null;
$tareasRetrasadasCount = isset($tareasRetrasadasCount) ? (int) $tareasRetrasadasCount : 0;

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

$estadosDisponibles = [];
foreach ($estadosLista as $estadoItem) {
    $estadoTexto = trim((string) $estadoItem);
    if ($estadoTexto === '') {
        continue;
    }
    if (!array_key_exists($estadoTexto, $estadosPanel)) {
        continue;
    }
    if (!in_array($estadoTexto, $estadosDisponibles, true)) {
        $estadosDisponibles[] = $estadoTexto;
    }
}
if (empty($estadosDisponibles)) {
    $estadosDisponibles = array_keys($estadosPanel);
}

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
?>

<div class="panel" id="panelApp">
    <?php require APP_ROOT . '/app/views/layouts/panel_aside.php'; ?>

    <div class="fondo-menu" id="fondoMenu" aria-hidden="true"></div>

    <main class="contenido">
        <div class="panel-notificaciones">
            <?php if (!empty($notificacionTarea)): ?>
                <section class="notificacion-panel" role="alert" aria-live="polite">
                    <div class="notificacion-panel-titulo">
                        <span>Nueva tarea asignada</span>
                    </div>

                    <div class="notificacion-panel-cuerpo">
                        <div class="notificacion-panel-avatar">
                            <img
                                src="<?= BASE_URL . 'img/' . htmlspecialchars((string) $notificacionTarea['imagen']) ?>"
                                alt="Imagen de <?= htmlspecialchars((string) $notificacionTarea['creador_nombre']) ?>">
                        </div>

                        <div class="notificacion-panel-info">
                            <p class="notificacion-panel-usuario">
                                <?= htmlspecialchars((string) $notificacionTarea['creador_nombre']) ?>
                            </p>

                            <p class="notificacion-panel-texto">
                                Te ha asignado una tarea de tipo
                                <strong><?= htmlspecialchars((string) $notificacionTarea['tipo_actividad']) ?></strong>
                                para el lead
                                <strong><?= htmlspecialchars((string) $notificacionTarea['lead_nombre']) ?></strong>.
                            </p>

                            <?php if (!empty($notificacionTarea['fecha_final'])): ?>
                                <p class="notificacion-panel-fecha">
                                    Seguimiento: <?= htmlspecialchars((string) date('d/m/Y', strtotime((string) $notificacionTarea['fecha_final']))) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="notificacion-panel-acciones">
                        <button type="button" class="boton-cerrar-notificacion" data-cerrar-notificacion>
                            Cerrar
                        </button>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($tareasRetrasadasCount > 0): ?>
                <section class="notificacion-panel notificacion-panel-alerta" role="alert" aria-live="polite">
                    <div class="notificacion-panel-titulo">
                        <span>Tareas retrasadas</span>
                    </div>

                    <div class="notificacion-panel-cuerpo">
                        <div class="notificacion-panel-icono" aria-hidden="true">⏰</div>

                        <div class="notificacion-panel-info">
                            <p class="notificacion-panel-usuario">Aviso comercial</p>
                            <p class="notificacion-panel-texto">
                                Tienes <strong><?= $tareasRetrasadasCount ?></strong>
                                tarea<?= ($tareasRetrasadasCount !== 1) ? 's' : '' ?>
                                retrasada<?= ($tareasRetrasadasCount !== 1) ? 's' : '' ?> que requieren seguimiento.
                            </p>
                        </div>
                    </div>

                    <div class="notificacion-panel-acciones">
                        <a href="<?= BASE_URL . 'tareas' ?>" class="enlace-notificacion-panel">
                            Ver tareas
                        </a>
                        <button type="button" class="boton-cerrar-notificacion" data-cerrar-notificacion>
                            Cerrar
                        </button>
                    </div>
                </section>
            <?php endif; ?>
        </div>

        <header class="cabecera-panel">
            <div class="cabecera-info">
                <p class="cabecera-etiqueta">CRM PipelineDesk</p>
                <h1>Control del embudo comercial</h1>
                <p class="cabecera-texto">
                    Panel principal para gestionar el avance de tus leads
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

                <a href="<?= BASE_URL . 'tareas' ?>" class="boton boton-volver">Ver tareas</a>

                <?php require APP_ROOT . '/app/views/layouts/theme_toggle.php'; ?>

                <a href="<?= BASE_URL . 'logout' ?>" class="boton boton-volver" aria-label="Cerrar sesión" title="Cerrar sesión">
                    Salir
                </a>

                <div class="usuario">
                    <span class="usuario-nombre"><?= htmlspecialchars($nombreUsuario) ?></span>
                    <span class="usuario-rol"><?= htmlspecialchars($rolUsuario) ?></span>
                </div>
            </div>
        </header>

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
                                    $claseEstado = $estadosPanel[$estadoActual]['clase'] ?? 'estado-nuevo';
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
                                                    <div
                                                        class="selector-estado-custom"
                                                        data-selector-estado
                                                        data-selector-actual="<?= htmlspecialchars($estadoActual) ?>">

                                                        <input type="hidden" name="estado" value="<?= htmlspecialchars($estadoActual) ?>" data-selector-input>

                                                        <button
                                                            type="button"
                                                            class="selector-estado-trigger <?= htmlspecialchars($claseEstado) ?>"
                                                            data-selector-trigger
                                                            aria-haspopup="listbox"
                                                            aria-expanded="false">
                                                            <span class="selector-estado-trigger-texto"><?= htmlspecialchars($estadoActual) ?></span>
                                                            <span class="selector-estado-trigger-icono" aria-hidden="true">▾</span>
                                                        </button>

                                                        <div class="selector-estado-template" data-selector-template hidden>
                                                            <?php foreach ($estadosDisponibles as $estadoItem): ?>
                                                                <?php
                                                                $estadoItemTexto = (string) $estadoItem;
                                                                $claseEstadoItem = $estadosPanel[$estadoItemTexto]['clase'] ?? 'estado-nuevo';
                                                                ?>
                                                                <button
                                                                    type="button"
                                                                    class="selector-estado-opcion <?= htmlspecialchars($claseEstadoItem) ?>"
                                                                    data-estado="<?= htmlspecialchars($estadoItemTexto) ?>">
                                                                    <?= htmlspecialchars($estadoItemTexto) ?>
                                                                </button>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
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
