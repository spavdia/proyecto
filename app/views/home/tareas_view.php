<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Tareas';
$archivoCssVista = 'tareas.css';
$archivoJsVista = 'tareas.js';
$menuActivo = 'tareas';

$__ctx = get_defined_vars();

$usuario = (isset($__ctx['usuario']) && is_array($__ctx['usuario'])) ? $__ctx['usuario'] : [];
$tareas = (isset($__ctx['tareas']) && is_array($__ctx['tareas'])) ? $__ctx['tareas'] : [];
$usuarios = (isset($__ctx['usuarios']) && is_array($__ctx['usuarios'])) ? $__ctx['usuarios'] : [];
$leads = (isset($__ctx['leads']) && is_array($__ctx['leads'])) ? $__ctx['leads'] : [];
$tiposActividad = (isset($__ctx['tiposActividad']) && is_array($__ctx['tiposActividad'])) ? $__ctx['tiposActividad'] : [];
$tiposBloqueo = (isset($__ctx['tiposBloqueo']) && is_array($__ctx['tiposBloqueo'])) ? $__ctx['tiposBloqueo'] : [];
$solucionesBloqueo = (isset($__ctx['solucionesBloqueo']) && is_array($__ctx['solucionesBloqueo'])) ? $__ctx['solucionesBloqueo'] : [];
$estadosTarea = (isset($__ctx['estadosTarea']) && is_array($__ctx['estadosTarea'])) ? $__ctx['estadosTarea'] : [];
$nuevasAsignadas = (isset($__ctx['nuevasAsignadas']) && is_array($__ctx['nuevasAsignadas'])) ? $__ctx['nuevasAsignadas'] : [];
$resumenUsuariosAdmin = (isset($__ctx['resumenUsuariosAdmin']) && is_array($__ctx['resumenUsuariosAdmin'])) ? $__ctx['resumenUsuariosAdmin'] : [];
$proximosSeguimientos = (isset($__ctx['proximosSeguimientos']) && is_array($__ctx['proximosSeguimientos'])) ? $__ctx['proximosSeguimientos'] : [];
$errores = (isset($__ctx['errores']) && is_array($__ctx['errores'])) ? $__ctx['errores'] : [];
$datosForm = (isset($__ctx['datosForm']) && is_array($__ctx['datosForm'])) ? $__ctx['datosForm'] : [];
$erroresEdicion = (isset($__ctx['erroresEdicion']) && is_array($__ctx['erroresEdicion'])) ? $__ctx['erroresEdicion'] : [];
$datosEdicion = (isset($__ctx['datosEdicion']) && is_array($__ctx['datosEdicion'])) ? $__ctx['datosEdicion'] : [];
$bloqueosResumen = (isset($__ctx['bloqueosResumen']) && is_array($__ctx['bloqueosResumen'])) ? $__ctx['bloqueosResumen'] : [];
$resumenEstados = (isset($__ctx['resumenEstados']) && is_array($__ctx['resumenEstados'])) ? $__ctx['resumenEstados'] : [];

$mostrarFormulario = isset($__ctx['mostrarFormulario']) ? (bool) $__ctx['mostrarFormulario'] : false;
$editarId = isset($__ctx['editarId']) ? (int) $__ctx['editarId'] : 0;
$retrasadasCount = isset($__ctx['retrasadasCount']) ? (int) $__ctx['retrasadasCount'] : 0;

require_once APP_ROOT . '/app/views/layouts/header.php';

$nombreUsuario = (string) ($usuario['nombre'] ?? 'Usuario');
$rolUsuario = (string) ($usuario['rol'] ?? 'ventas');
$usuarioActualId = (int) ($usuario['id'] ?? 0);
$esAdmin = (($usuario['rol'] ?? '') === 'admin');

if (!function_exists('tareas_valor_form')) {
    function tareas_valor_form(array $datos, string $clave, string $defecto = ''): string
    {
        return (string) ($datos[$clave] ?? $defecto);
    }
}

if (!function_exists('tareas_fecha_simple')) {
    function tareas_fecha_simple(?string $fecha): string
    {
        if (empty($fecha)) {
            return '-';
        }

        $timestamp = strtotime($fecha);
        return $timestamp ? date('d/m/Y', $timestamp) : (string) $fecha;
    }
}

$leadEstadoSeleccionado = '';
$leadIdForm = (int) ($datosForm['lead_id'] ?? 0);

foreach ($leads as $leadItem) {
    if ((int) ($leadItem['id'] ?? 0) === $leadIdForm) {
        $leadEstadoSeleccionado = (string) ($leadItem['estado'] ?? '');
        break;
    }
}

$mostrarObjecionesForm = (tareas_valor_form($datosForm, 'tipo_actividad') === 'Objeciones' && $leadEstadoSeleccionado === 'Objeciones');

$bloqueosResumen = array_merge([
    'abiertos' => 0,
    'resueltos' => 0,
    'porcentaje' => 0,
    'total' => 0
], $bloqueosResumen);

$resumenEstados = array_merge([
    'Pendiente' => 0,
    'En curso'  => 0,
    'Terminada' => 0
], $resumenEstados);

$pendientes = (int) ($resumenEstados['Pendiente'] ?? 0);
$enCurso = (int) ($resumenEstados['En curso'] ?? 0);
$terminadas = (int) ($resumenEstados['Terminada'] ?? 0);
$totalEstados = $pendientes + $enCurso + $terminadas;
$porcentajeSemaforo = $totalEstados > 0 ? (int) round(($terminadas / $totalEstados) * 100) : 0;
$posicionIndicador = max(2, min(98, $porcentajeSemaforo));

$claseInputEditar = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20';
$claseSelectEditar = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20';
$claseTextareaEditar = 'w-full rounded-2xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6 text-slate-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20 resize-y min-h-[110px]';
$claseLabelEditar = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400';
?>

<div class="panel" id="tareasApp">
    <?php require APP_ROOT . '/app/views/layouts/panel_aside.php'; ?>

    <div class="fondo-menu" id="fondoMenu" aria-hidden="true"></div>

    <main class="contenido">
        <header class="cabecera-tareas">
            <div class="cabecera-info">
                <p class="cabecera-etiqueta">Productividad comercial</p>
                <h1>Vista de tareas</h1>
                <p class="cabecera-texto">
                    Gestiona seguimientos, actividades y tareas asignadas desde una única vista operativa.
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

                <a href="<?= BASE_URL . 'panel' ?>" class="boton boton-volver">Volver al panel</a>

                <button
                    type="button"
                    class="boton boton-principal"
                    id="botonNuevaTarea"
                    aria-expanded="<?= $mostrarFormulario ? 'true' : 'false' ?>"
                    aria-controls="panelFormularioTarea">
                    Nueva tarea
                </button>

                <?php require APP_ROOT . '/app/views/layouts/theme_toggle.php'; ?>

                <div class="usuario">
                    <span class="usuario-nombre"><?= htmlspecialchars($nombreUsuario) ?></span>
                    <span class="usuario-rol"><?= htmlspecialchars($rolUsuario) ?></span>
                </div>
            </div>
        </header>

        <?php if (!empty($nuevasAsignadas)): ?>
            <section class="aviso-tareas">
                <div class="aviso-top">
                    <h2>Tareas nuevas asignadas</h2>
                    <span class="aviso-total"><?= count($nuevasAsignadas) ?></span>
                </div>

                <div class="aviso-lista">
                    <?php foreach ($nuevasAsignadas as $nueva): ?>
                        <article class="aviso-item">
                            <strong><?= htmlspecialchars((string) ($nueva['tipo_actividad'] ?? 'Actividad')) ?></strong>
                            <span><?= htmlspecialchars((string) ($nueva['lead_nombre'] ?? 'Lead')) ?></span>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="resumen-tareas">
            <article class="resumen-card resumen-conteo">
                <div class="resumen-card-top">
                    <h2>Bloqueos por resolver</h2>
                    <span><?= (int) ($bloqueosResumen['total'] ?? 0) ?> total</span>
                </div>

                <div class="conteo-grid">
                    <div class="conteo-item conteo-pendiente">
                        <small>Abiertos</small>
                        <strong><?= (int) ($bloqueosResumen['abiertos'] ?? 0) ?></strong>
                    </div>

                    <div class="conteo-item conteo-terminada">
                        <small>Resueltos</small>
                        <strong><?= (int) ($bloqueosResumen['resueltos'] ?? 0) ?></strong>
                    </div>
                </div>

                <div class="conteo-barra">
                    <div class="conteo-barra-valor" style="width: <?= (int) ($bloqueosResumen['porcentaje'] ?? 0) ?>%;"></div>
                </div>

                <p class="conteo-texto"><?= (int) ($bloqueosResumen['porcentaje'] ?? 0) ?>% de bloqueos resueltos</p>
            </article>

            <article class="resumen-card resumen-estados">
                <div class="resumen-card-top">
                    <h2>Tareas por estado</h2>
                    <span>Semáforo KPI</span>
                </div>

                <div class="semaforo-kpi" aria-label="Indicador semáforo del porcentaje de tareas terminadas">
                    <div class="semaforo-kpi-top">
                        <small>Indicador</small>
                        <strong><?= $porcentajeSemaforo ?>%</strong>
                    </div>

                    <div class="semaforo-kpi-panel">
                        <div class="semaforo-indicador" style="left: <?= $posicionIndicador ?>%;">
                            <span class="semaforo-indicador-triangulo"></span>
                        </div>

                        <div class="semaforo-kpi-barra">
                            <div class="tramo tramo-rojo" aria-hidden="true"></div>
                            <div class="tramo tramo-amarillo" aria-hidden="true"></div>
                            <div class="tramo tramo-verde" aria-hidden="true"></div>
                        </div>

                        <div class="semaforo-kpi-escala" aria-hidden="true">
                            <span>0%</span>
                            <span>50%</span>
                            <span>80%</span>
                            <span>100%</span>
                        </div>
                    </div>

                    <div class="semaforo-kpi-leyenda">
                        <div class="leyenda-pill leyenda-pill-rojo">
                            <span><span class="leyenda-dot"></span> Pendientes</span>
                            <strong><?= $pendientes ?></strong>
                        </div>

                        <div class="leyenda-pill leyenda-pill-amarillo">
                            <span><span class="leyenda-dot"></span> En curso</span>
                            <strong><?= $enCurso ?></strong>
                        </div>

                        <div class="leyenda-pill leyenda-pill-verde">
                            <span><span class="leyenda-dot"></span> Terminadas</span>
                            <strong><?= $terminadas ?></strong>
                        </div>
                    </div>
                </div>
            </article>

            <article class="resumen-card resumen-seguimientos">
                <div class="resumen-card-top">
                    <h2>Próximos seguimientos</h2>
                    <span>3 más cercanos</span>
                </div>

                <?php if (empty($proximosSeguimientos)): ?>
                    <p class="seguimiento-vacio">Sin próximos seguimientos.</p>
                <?php else: ?>
                    <ul class="seguimiento-lista">
                        <?php foreach ($proximosSeguimientos as $seguimiento): ?>
                            <li>
                                <span class="seguimiento-fecha"><?= htmlspecialchars(tareas_fecha_simple((string) ($seguimiento['fecha_final'] ?? ''))) ?></span>
                                <div class="seguimiento-contenido">
                                    <strong><?= htmlspecialchars((string) ($seguimiento['lead_nombre'] ?? 'Lead')) ?></strong>
                                    <small><?= htmlspecialchars((string) ($seguimiento['tipo_actividad'] ?? 'Actividad')) ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>

            <article class="resumen-card resumen-retrasadas">
                <div class="resumen-card-top">
                    <h2>Tareas retrasadas</h2>
                    <span>Alerta</span>
                </div>

                <div class="retrasadas-box">
                    <span class="retrasadas-icono" aria-hidden="true">⏰</span>
                    <strong><?= $retrasadasCount ?></strong>
                </div>
            </article>
        </section>

        <?php if ($esAdmin): ?>
            <section class="resumen-admin">
                <div class="resumen-admin-top">
                    <h2>Usuarios con tareas</h2>
                    <p>Porcentaje de tareas terminadas por usuario.</p>
                </div>

                <?php if (empty($resumenUsuariosAdmin)): ?>
                    <p class="resumen-admin-vacio">No hay usuarios con tareas asignadas.</p>
                <?php else: ?>
                    <div class="resumen-admin-grid">
                        <?php foreach ($resumenUsuariosAdmin as $filaUsuario): ?>
                            <article class="usuario-tareas-card">
                                <div class="usuario-tareas-top">
                                    <strong><?= htmlspecialchars((string) ($filaUsuario['nombre'] ?? 'Usuario')) ?></strong>
                                    <span><?= htmlspecialchars((string) ($filaUsuario['rol'] ?? 'ventas')) ?></span>
                                </div>

                                <p class="usuario-tareas-meta">
                                    <?= (int) ($filaUsuario['tareas_terminadas'] ?? 0) ?> / <?= (int) ($filaUsuario['total_tareas'] ?? 0) ?> terminadas
                                </p>

                                <div class="barra-progreso">
                                    <div class="barra-progreso-valor" style="width: <?= (int) ($filaUsuario['porcentaje_terminadas'] ?? 0) ?>%;"></div>
                                </div>

                                <p class="usuario-tareas-porcentaje">
                                    <?= (int) ($filaUsuario['porcentaje_terminadas'] ?? 0) ?>%
                                </p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <section class="tareas-layout <?= $mostrarFormulario ? 'con-formulario' : '' ?>" id="tareasLayout">
            <aside class="panel-formulario-tarea" id="panelFormularioTarea" <?= $mostrarFormulario ? '' : 'hidden' ?>>
                <div class="bloque">
                    <div class="bloque-top">
                        <h2>Nueva tarea</h2>
                        <button type="button" class="boton-cerrar-formulario" id="botonCerrarFormulario" aria-label="Cerrar formulario">✕</button>
                    </div>

                    <form action="<?= BASE_URL . 'tareas/guardar' ?>" method="POST" class="form-tarea">
                        <div class="campo">
                            <label for="lead_id">Lead</label>
                            <select id="lead_id" name="lead_id">
                                <option value="">Selecciona un lead</option>
                                <?php foreach ($leads as $lead): ?>
                                    <option
                                        value="<?= (int) ($lead['id'] ?? 0) ?>"
                                        data-estado="<?= htmlspecialchars((string) ($lead['estado'] ?? '')) ?>"
                                        <?= ((int) tareas_valor_form($datosForm, 'lead_id') === (int) ($lead['id'] ?? 0)) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) ($lead['lead_nombre'] ?? '')) ?> - <?= htmlspecialchars((string) ($lead['estado'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-campo"><?= !empty($errores['lead_id']) ? htmlspecialchars((string) $errores['lead_id']) : '' ?></span>
                        </div>

                        <div class="campo">
                            <label for="usuario_asignado_id">Asignar a</label>
                            <?php $asignadoSeleccionado = (int) ($datosForm['usuario_asignado_id'] ?? $usuarioActualId); ?>
                            <select id="usuario_asignado_id" name="usuario_asignado_id">
                                <option value="">Selecciona un usuario</option>
                                <?php foreach ($usuarios as $usuarioItem): ?>
                                    <option
                                        value="<?= (int) ($usuarioItem['id'] ?? 0) ?>"
                                        <?= ($asignadoSeleccionado === (int) ($usuarioItem['id'] ?? 0)) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) ($usuarioItem['nombre'] ?? '')) ?> (<?= htmlspecialchars((string) ($usuarioItem['rol'] ?? '')) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-campo"><?= !empty($errores['usuario_asignado_id']) ? htmlspecialchars((string) $errores['usuario_asignado_id']) : '' ?></span>
                        </div>

                        <div class="campo">
                            <label for="tipo_actividad">Actividad</label>
                            <select id="tipo_actividad" name="tipo_actividad">
                                <option value="">Selecciona una actividad</option>
                                <?php foreach ($tiposActividad as $tipo): ?>
                                    <option
                                        value="<?= htmlspecialchars((string) $tipo) ?>"
                                        <?= (tareas_valor_form($datosForm, 'tipo_actividad') === (string) $tipo) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) $tipo) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-campo"><?= !empty($errores['tipo_actividad']) ? htmlspecialchars((string) $errores['tipo_actividad']) : '' ?></span>
                        </div>

                        <div class="campo campo-objeciones <?= $mostrarObjecionesForm ? 'visible' : '' ?>" id="bloqueObjecionesFormulario" <?= $mostrarObjecionesForm ? '' : 'hidden' ?>>
                            <label>Objeciones</label>

                            <div class="objeciones-grid">
                                <div>
                                    <label for="tipo_bloqueo" class="sub-label">Tipo de bloqueo</label>
                                    <select id="tipo_bloqueo" name="tipo_bloqueo">
                                        <?php foreach ($tiposBloqueo as $bloqueo): ?>
                                            <option
                                                value="<?= htmlspecialchars((string) $bloqueo) ?>"
                                                <?= (tareas_valor_form($datosForm, 'tipo_bloqueo', 'Definir') === (string) $bloqueo) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string) $bloqueo) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="error-campo"><?= !empty($errores['tipo_bloqueo']) ? htmlspecialchars((string) $errores['tipo_bloqueo']) : '' ?></span>
                                </div>

                                <div>
                                    <label for="solucion_bloqueo" class="sub-label">Solución propuesta</label>
                                    <select id="solucion_bloqueo" name="solucion_bloqueo">
                                        <?php foreach ($solucionesBloqueo as $solucion): ?>
                                            <option
                                                value="<?= htmlspecialchars((string) $solucion) ?>"
                                                <?= (tareas_valor_form($datosForm, 'solucion_bloqueo', 'Definir') === (string) $solucion) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string) $solucion) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="error-campo"><?= !empty($errores['solucion_bloqueo']) ? htmlspecialchars((string) $errores['solucion_bloqueo']) : '' ?></span>
                                </div>
                            </div>

                            <p class="ayuda-objeciones">Solo aparece si eliges la actividad Objeciones y el lead está en la etapa Objeciones.</p>
                        </div>

                        <div class="campo">
                            <label for="fecha_final">Fecha final</label>
                            <input type="date" id="fecha_final" name="fecha_final" value="<?= htmlspecialchars(tareas_valor_form($datosForm, 'fecha_final')) ?>">
                            <span class="error-campo"><?= !empty($errores['fecha_final']) ? htmlspecialchars((string) $errores['fecha_final']) : '' ?></span>
                        </div>

                        <div class="campo">
                            <label for="estado">Estado</label>
                            <?php $estadoSeleccionado = tareas_valor_form($datosForm, 'estado', 'Pendiente'); ?>
                            <select id="estado" name="estado">
                                <?php foreach ($estadosTarea as $estadoItem): ?>
                                    <option
                                        value="<?= htmlspecialchars((string) $estadoItem) ?>"
                                        <?= ($estadoSeleccionado === (string) $estadoItem) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) $estadoItem) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-campo"><?= !empty($errores['estado']) ? htmlspecialchars((string) $errores['estado']) : '' ?></span>
                        </div>

                        <div class="campo">
                            <label for="descripcion">Nota</label>
                            <textarea id="descripcion" name="descripcion" rows="5"><?= htmlspecialchars(tareas_valor_form($datosForm, 'descripcion')) ?></textarea>
                            <span class="error-campo"><?= !empty($errores['descripcion']) ? htmlspecialchars((string) $errores['descripcion']) : '' ?></span>
                        </div>

                        <div class="acciones-formulario">
                            <button type="submit" class="boton boton-principal">Guardar tarea</button>
                        </div>
                    </form>
                </div>
            </aside>

            <section class="panel-tabla-tareas">
                <div class="bloque">
                    <div class="bloque-top">
                        <h2><?= $esAdmin ? 'Todas las tareas' : 'Mis tareas' ?></h2>
                    </div>

                    <div class="tabla-wrap">
                        <table class="tabla-panel">
                            <thead>
                                <tr>
                                    <th>Lead</th>
                                    <th>Actividad</th>
                                    <th>Asignado a</th>
                                    <th>Fecha final</th>
                                    <th>Estado</th>
                                    <th>Nota</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tareas)): ?>
                                    <tr>
                                        <td colspan="7" class="tabla-vacia">No hay tareas registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tareas as $tarea): ?>
                                        <?php
                                        $tareaId = (int) ($tarea['id'] ?? 0);
                                        $leadId = (int) ($tarea['lead_id'] ?? 0);
                                        $esEditando = $editarId === $tareaId;
                                        $formId = 'form-editar-tarea-' . $tareaId;

                                        $fechaInput = !empty($datosEdicion['fecha_final']) && $esEditando
                                            ? (string) $datosEdicion['fecha_final']
                                            : (!empty($tarea['fecha_final']) ? date('Y-m-d', strtotime((string) $tarea['fecha_final'])) : '');

                                        $descripcionEdit = $esEditando
                                            ? (string) ($datosEdicion['descripcion'] ?? (string) ($tarea['descripcion'] ?? ''))
                                            : '';

                                        $estadoEdit = $esEditando
                                            ? (string) ($datosEdicion['estado'] ?? (string) ($tarea['estado'] ?? ''))
                                            : '';

                                        $tipoBloqueoEdit = $esEditando
                                            ? (string) ($datosEdicion['tipo_bloqueo'] ?? (string) ($tarea['tipo_bloqueo'] ?? 'Definir'))
                                            : '';

                                        $solucionBloqueoEdit = $esEditando
                                            ? (string) ($datosEdicion['solucion_bloqueo'] ?? (string) ($tarea['solucion_bloqueo'] ?? 'Definir'))
                                            : '';

                                        $claseEstado = 'estado-tarea';
                                        if (($tarea['estado'] ?? '') === 'Pendiente') {
                                            $claseEstado .= ' estado-pendiente';
                                        } elseif (($tarea['estado'] ?? '') === 'En curso') {
                                            $claseEstado .= ' estado-curso';
                                        } else {
                                            $claseEstado .= ' estado-terminada';
                                        }

                                        $esRetrasada = (
                                            !empty($tarea['fecha_final']) &&
                                            strtotime(date('Y-m-d', strtotime((string) $tarea['fecha_final']))) < strtotime(date('Y-m-d')) &&
                                            (string) ($tarea['estado'] ?? '') !== 'Terminada'
                                        );

                                        $esTareaObjecion = ((string) ($tarea['tipo_actividad'] ?? '') === 'Objeciones');
                                        $claseLeadObjecion = ((string) ($tarea['lead_estado'] ?? '') === 'Objeciones') ? 'lead-objecion' : '';

                                        $descripcionRapida = (string) ($tarea['descripcion'] ?? '');
                                        $fechaRapida = !empty($tarea['fecha_final']) ? date('Y-m-d', strtotime((string) $tarea['fecha_final'])) : '';
                                        $tipoBloqueoRapido = (string) ($tarea['tipo_bloqueo'] ?? 'Definir');
                                        $solucionBloqueoRapido = (string) ($tarea['solucion_bloqueo'] ?? 'Definir');
                                        ?>

                                        <?php if ($esEditando): ?>
                                            <form id="<?= htmlspecialchars($formId) ?>" action="<?= BASE_URL . 'tareas/' . $tareaId . '/actualizar' ?>" method="POST"></form>
                                        <?php endif; ?>

                                        <tr class="<?= trim(($esEditando ? 'fila-edicion ' : '') . ($esRetrasada ? 'fila-tarea-retrasada' : '')) ?>">
                                            <td class="celda-lead-tarea <?= htmlspecialchars($claseLeadObjecion) ?>">
                                                <a href="<?= BASE_URL . 'leads/' . $leadId ?>" class="enlace-lead-tarea">
                                                    <?= htmlspecialchars((string) ($tarea['lead_nombre'] ?? '-')) ?>
                                                </a>
                                            </td>

                                            <td><?= htmlspecialchars((string) ($tarea['tipo_actividad'] ?? '-')) ?></td>

                                            <td><?= htmlspecialchars((string) ($tarea['asignado_nombre'] ?? '-')) ?></td>

                                            <td>
                                                <?php if ($esEditando): ?>
                                                    <label class="<?= $claseLabelEditar ?>">Fecha final</label>
                                                    <input
                                                        type="date"
                                                        name="fecha_final"
                                                        form="<?= htmlspecialchars($formId) ?>"
                                                        class="<?= $claseInputEditar ?>"
                                                        value="<?= htmlspecialchars($fechaInput) ?>">
                                                    <span class="error-campo"><?= !empty($erroresEdicion['fecha_final']) ? htmlspecialchars((string) $erroresEdicion['fecha_final']) : '' ?></span>
                                                <?php else: ?>
                                                    <span class="<?= $esRetrasada ? 'fecha-retrasada' : '' ?>">
                                                        <?= htmlspecialchars(tareas_fecha_simple((string) ($tarea['fecha_final'] ?? ''))) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if ($esEditando): ?>
                                                    <label class="<?= $claseLabelEditar ?>">Estado</label>
                                                    <select
                                                        name="estado"
                                                        form="<?= htmlspecialchars($formId) ?>"
                                                        class="<?= $claseSelectEditar ?>">
                                                        <?php foreach ($estadosTarea as $estadoItem): ?>
                                                            <option
                                                                value="<?= htmlspecialchars((string) $estadoItem) ?>"
                                                                <?= ($estadoEdit === (string) $estadoItem) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars((string) $estadoItem) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span class="error-campo"><?= !empty($erroresEdicion['estado']) ? htmlspecialchars((string) $erroresEdicion['estado']) : '' ?></span>
                                                <?php else: ?>
                                                    <form action="<?= BASE_URL . 'tareas/' . $tareaId . '/actualizar' ?>" method="POST" class="form-estado-rapido">
                                                        <input type="hidden" name="quick_update" value="1">
                                                        <input type="hidden" name="descripcion" value="<?= htmlspecialchars($descripcionRapida, ENT_QUOTES, 'UTF-8') ?>">
                                                        <input type="hidden" name="fecha_final" value="<?= htmlspecialchars($fechaRapida, ENT_QUOTES, 'UTF-8') ?>">

                                                        <?php if ($esTareaObjecion): ?>
                                                            <input type="hidden" name="tipo_bloqueo" value="<?= htmlspecialchars($tipoBloqueoRapido, ENT_QUOTES, 'UTF-8') ?>">
                                                            <input type="hidden" name="solucion_bloqueo" value="<?= htmlspecialchars($solucionBloqueoRapido, ENT_QUOTES, 'UTF-8') ?>">
                                                        <?php endif; ?>

                                                        <select name="estado" class="selector-estado-tabla <?= htmlspecialchars($claseEstado) ?>" data-autosubmit-estado="1" aria-label="Cambiar estado de la tarea">
                                                            <?php foreach ($estadosTarea as $estadoItem): ?>
                                                                <option
                                                                    value="<?= htmlspecialchars((string) $estadoItem) ?>"
                                                                    <?= (((string) ($tarea['estado'] ?? '')) === (string) $estadoItem) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars((string) $estadoItem) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </form>
                                                <?php endif; ?>
                                            </td>

                                            <td class="texto-nota">
                                                <?php if ($esEditando): ?>
                                                    <?php if ($esTareaObjecion): ?>
                                                        <div class="grid gap-3 mb-3 md:grid-cols-2">
                                                            <div>
                                                                <label class="<?= $claseLabelEditar ?>">Bloqueo</label>
                                                                <select
                                                                    name="tipo_bloqueo"
                                                                    form="<?= htmlspecialchars($formId) ?>"
                                                                    class="<?= $claseSelectEditar ?>">
                                                                    <?php foreach ($tiposBloqueo as $bloqueo): ?>
                                                                        <option
                                                                            value="<?= htmlspecialchars((string) $bloqueo) ?>"
                                                                            <?= ($tipoBloqueoEdit === (string) $bloqueo) ? 'selected' : '' ?>>
                                                                            <?= htmlspecialchars((string) $bloqueo) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <span class="error-campo"><?= !empty($erroresEdicion['tipo_bloqueo']) ? htmlspecialchars((string) $erroresEdicion['tipo_bloqueo']) : '' ?></span>
                                                            </div>

                                                            <div>
                                                                <label class="<?= $claseLabelEditar ?>">Solución</label>
                                                                <select
                                                                    name="solucion_bloqueo"
                                                                    form="<?= htmlspecialchars($formId) ?>"
                                                                    class="<?= $claseSelectEditar ?>">
                                                                    <?php foreach ($solucionesBloqueo as $solucion): ?>
                                                                        <option
                                                                            value="<?= htmlspecialchars((string) $solucion) ?>"
                                                                            <?= ($solucionBloqueoEdit === (string) $solucion) ? 'selected' : '' ?>>
                                                                            <?= htmlspecialchars((string) $solucion) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <span class="error-campo"><?= !empty($erroresEdicion['solucion_bloqueo']) ? htmlspecialchars((string) $erroresEdicion['solucion_bloqueo']) : '' ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <label class="<?= $claseLabelEditar ?>">Nota</label>
                                                    <textarea
                                                        name="descripcion"
                                                        form="<?= htmlspecialchars($formId) ?>"
                                                        rows="4"
                                                        class="<?= $claseTextareaEditar ?>"><?= htmlspecialchars($descripcionEdit) ?></textarea>
                                                    <span class="error-campo"><?= !empty($erroresEdicion['descripcion']) ? htmlspecialchars((string) $erroresEdicion['descripcion']) : '' ?></span>
                                                <?php else: ?>
                                                    <?php if ($esTareaObjecion): ?>
                                                        <div class="bloqueo-chips">
                                                            <span class="chip-bloqueo"><?= htmlspecialchars((string) ($tarea['tipo_bloqueo'] ?? 'Definir')) ?></span>
                                                            <span class="chip-solucion"><?= htmlspecialchars((string) ($tarea['solucion_bloqueo'] ?? 'Definir')) ?></span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?= htmlspecialchars(mb_strimwidth((string) ($tarea['descripcion'] ?? ''), 0, 140, '...')) ?>
                                                <?php endif; ?>
                                            </td>

                                            <td class="acciones-celda">
                                                <?php if ($esEditando): ?>
                                                    <button type="submit" form="<?= htmlspecialchars($formId) ?>" class="boton-icono guardar" title="Guardar">💾</button>
                                                    <a href="<?= BASE_URL . 'tareas' ?>" class="boton-icono cancelar" title="Cancelar">✕</a>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL . 'tareas?editar=' . $tareaId ?>" class="boton-icono editar" title="Editar">✏</a>

                                                    <form action="<?= BASE_URL . 'tareas/' . $tareaId . '/eliminar' ?>" method="POST" class="form-eliminar-tarea">
                                                        <button type="submit" class="boton-icono eliminar" title="Eliminar">🗑</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </section>
    </main>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>