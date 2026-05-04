<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Dashboard';
$archivoCssVista = 'dashboard.css';
$archivoJsVista = 'dashboard.js';
$menuActivo = 'dashboard';

$__ctx = get_defined_vars();

$usuario = (isset($__ctx['usuario']) && is_array($__ctx['usuario'])) ? $__ctx['usuario'] : [];
$filtros = (isset($__ctx['filtros']) && is_array($__ctx['filtros'])) ? $__ctx['filtros'] : [];
$usuariosLista = (isset($__ctx['usuariosLista']) && is_array($__ctx['usuariosLista'])) ? $__ctx['usuariosLista'] : [];
$serviciosLista = (isset($__ctx['serviciosLista']) && is_array($__ctx['serviciosLista'])) ? $__ctx['serviciosLista'] : [];
$estadosLista = (isset($__ctx['estadosLista']) && is_array($__ctx['estadosLista'])) ? $__ctx['estadosLista'] : [];
$resumenGeneral = (isset($__ctx['resumenGeneral']) && is_array($__ctx['resumenGeneral'])) ? $__ctx['resumenGeneral'] : [];
$resumenPipeline = (isset($__ctx['resumenPipeline']) && is_array($__ctx['resumenPipeline'])) ? $__ctx['resumenPipeline'] : [];
$resumenTareas = (isset($__ctx['resumenTareas']) && is_array($__ctx['resumenTareas'])) ? $__ctx['resumenTareas'] : [];
$objecionesPorTipo = (isset($__ctx['objecionesPorTipo']) && is_array($__ctx['objecionesPorTipo'])) ? $__ctx['objecionesPorTipo'] : [];
$solucionesMasUsadas = (isset($__ctx['solucionesMasUsadas']) && is_array($__ctx['solucionesMasUsadas'])) ? $__ctx['solucionesMasUsadas'] : [];
$seguimientosUrgentes = (isset($__ctx['seguimientosUrgentes']) && is_array($__ctx['seguimientosUrgentes'])) ? $__ctx['seguimientosUrgentes'] : [];
$leadsSinContacto = (isset($__ctx['leadsSinContacto']) && is_array($__ctx['leadsSinContacto'])) ? $__ctx['leadsSinContacto'] : [];
$resumenUsuarios = (isset($__ctx['resumenUsuarios']) && is_array($__ctx['resumenUsuarios'])) ? $__ctx['resumenUsuarios'] : [];

$mensajeFlash = isset($__ctx['mensajeFlash']) ? $__ctx['mensajeFlash'] : null;
$iconoFlash = isset($__ctx['iconoFlash']) ? $__ctx['iconoFlash'] : null;
$claseFlash = isset($__ctx['claseFlash']) ? (string) $__ctx['claseFlash'] : 'info';

require_once APP_ROOT . '/app/views/layouts/header.php';

$nombreUsuario = (string) ($usuario['nombre'] ?? 'Usuario');
$rolUsuario = (string) ($usuario['rol'] ?? 'ventas');
$esAdmin = (($usuario['rol'] ?? '') === 'admin');

if (!function_exists('dashboard_money_eur')) {
    function dashboard_money_eur(float $valor): string
    {
        return number_format($valor, 2, ',', '.') . ' €';
    }
}

if (!function_exists('dashboard_fecha_es')) {
    function dashboard_fecha_es(?string $fecha): string
    {
        if (empty($fecha)) {
            return 'Sin fecha';
        }

        $timestamp = strtotime($fecha);
        return $timestamp ? date('d/m/Y', $timestamp) : (string) $fecha;
    }
}

if (!function_exists('dashboard_porcentaje_barra')) {
    function dashboard_porcentaje_barra(int|float $valor, int|float $maximo): float
    {
        if ($maximo <= 0) {
            return 0;
        }

        return round(($valor / $maximo) * 100, 1);
    }
}

$resumenGeneral = array_merge([
    'total_leads' => 0,
    'leads_ganados' => 0,
    'leads_perdidos' => 0,
    'leads_objeciones' => 0,
    'valor_pipeline' => 0,
    'valor_ganado' => 0,
    'conversion' => 0
], $resumenGeneral);

$resumenTareas = array_merge([
    'total' => 0,
    'pendientes' => 0,
    'terminadas' => 0,
    'retrasadas' => 0,
    'objeciones_abiertas' => 0,
    'objeciones_resueltas' => 0
], $resumenTareas);

$clasesEstado = [
    'Nuevo Lead'   => 'estado-nuevo',
    'Contactado'   => 'estado-contactado',
    'En Progreso'  => 'estado-progreso',
    'Objeciones'   => 'estado-objeciones',
    'Ganado'       => 'estado-ganado',
    'Perdido'      => 'estado-perdido'
];

$maxObjeciones = 0;
foreach ($objecionesPorTipo as $fila) {
    $maxObjeciones = max($maxObjeciones, (int) ($fila['total'] ?? 0));
}

$maxSoluciones = 0;
foreach ($solucionesMasUsadas as $fila) {
    $maxSoluciones = max($maxSoluciones, (int) ($fila['total'] ?? 0));
}
?>

<div class="panel" id="dashboardApp">
    <?php require APP_ROOT . '/app/views/layouts/panel_aside.php'; ?>

    <div class="fondo-menu" id="fondoMenu" aria-hidden="true"></div>

    <main class="contenido">
        <header class="cabecera-dashboard">
            <div class="cabecera-info">
                <p class="cabecera-etiqueta">Analítica comercial</p>
                <h1>Dashboard</h1>
                <p class="cabecera-texto">
                    Métricas del embudo, productividad comercial, objeciones y seguimiento operativo.
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

                <div class="usuario">
                    <span class="usuario-nombre"><?= htmlspecialchars($nombreUsuario) ?></span>
                    <span class="usuario-rol"><?= htmlspecialchars($rolUsuario) ?></span>
                </div>
            </div>
        </header>

        <?php if (!empty($mensajeFlash)): ?>
            <div class="mensaje-flash mensaje-<?= htmlspecialchars($claseFlash) ?>" role="alert" aria-live="assertive">
                <?php if (!empty($iconoFlash)): ?>
                    <span class="icono-flash" aria-hidden="true"><?= htmlspecialchars((string) $iconoFlash) ?></span>
                <?php endif; ?>
                <span><?= htmlspecialchars((string) $mensajeFlash) ?></span>
            </div>
        <?php endif; ?>

        <section class="bloque bloque-filtros">
            <div class="bloque-top">
                <h2>Filtros</h2>
            </div>

            <form action="<?= BASE_URL . 'dashboard' ?>" method="GET" class="form-filtros">
                <?php if ($esAdmin): ?>
                    <div class="campo">
                        <label for="usuario_id">Usuario</label>
                        <select id="usuario_id" name="usuario_id">
                            <option value="0">Todos</option>
                            <?php foreach ($usuariosLista as $usuarioItem): ?>
                                <option
                                    value="<?= (int) ($usuarioItem['id'] ?? 0) ?>"
                                    <?= ((int) ($filtros['usuario_id'] ?? 0) === (int) ($usuarioItem['id'] ?? 0)) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ($usuarioItem['nombre'] ?? 'Usuario')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="campo">
                    <label for="fecha_desde">Desde</label>
                    <input type="date" id="fecha_desde" name="fecha_desde" value="<?= htmlspecialchars((string) ($filtros['fecha_desde'] ?? '')) ?>">
                </div>

                <div class="campo">
                    <label for="fecha_hasta">Hasta</label>
                    <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?= htmlspecialchars((string) ($filtros['fecha_hasta'] ?? '')) ?>">
                </div>

                <div class="campo">
                    <label for="servicios">Servicio</label>
                    <select id="servicios" name="servicios">
                        <option value="">Todos</option>
                        <?php foreach ($serviciosLista as $servicio): ?>
                            <option
                                value="<?= htmlspecialchars((string) $servicio) ?>"
                                <?= (($filtros['servicios'] ?? '') === (string) $servicio) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $servicio) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="estado">Estado del lead</label>
                    <select id="estado" name="estado">
                        <option value="">Todos</option>
                        <?php foreach ($estadosLista as $estado): ?>
                            <option
                                value="<?= htmlspecialchars((string) $estado) ?>"
                                <?= (($filtros['estado'] ?? '') === (string) $estado) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $estado) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="origen">Origen</label>
                    <select id="origen" name="origen">
                        <option value="">Todos</option>
                        <option value="formulario_web" <?= (($filtros['origen'] ?? '') === 'formulario_web') ? 'selected' : '' ?>>Web</option>
                        <option value="app_interna" <?= (($filtros['origen'] ?? '') === 'app_interna') ? 'selected' : '' ?>>Interna</option>
                    </select>
                </div>

                <div class="acciones-filtros">
                    <button type="submit" class="boton boton-principal">Aplicar filtros</button>
                    <a href="<?= BASE_URL . 'dashboard' ?>" class="boton boton-volver">Limpiar</a>
                </div>
            </form>
        </section>

        <section class="metricas-grid">
            <article class="metrica-card">
                <p class="metrica-label">Leads filtrados</p>
                <strong class="metrica-valor"><?= (int) ($resumenGeneral['total_leads'] ?? 0) ?></strong>
            </article>

            <article class="metrica-card">
                <p class="metrica-label">Ganados</p>
                <strong class="metrica-valor"><?= (int) ($resumenGeneral['leads_ganados'] ?? 0) ?></strong>
            </article>

            <article class="metrica-card">
                <p class="metrica-label">Perdidos</p>
                <strong class="metrica-valor"><?= (int) ($resumenGeneral['leads_perdidos'] ?? 0) ?></strong>
            </article>

            <article class="metrica-card">
                <p class="metrica-label">Conversión</p>
                <strong class="metrica-valor"><?= htmlspecialchars((string) ($resumenGeneral['conversion'] ?? 0)) ?>%</strong>
            </article>

            <article class="metrica-card">
                <p class="metrica-label">Valor pipeline</p>
                <strong class="metrica-valor"><?= htmlspecialchars(dashboard_money_eur((float) ($resumenGeneral['valor_pipeline'] ?? 0))) ?></strong>
            </article>

            <article class="metrica-card">
                <p class="metrica-label">Valor ganado</p>
                <strong class="metrica-valor"><?= htmlspecialchars(dashboard_money_eur((float) ($resumenGeneral['valor_ganado'] ?? 0))) ?></strong>
            </article>

            <article class="metrica-card">
                <p class="metrica-label">Leads en objeciones</p>
                <strong class="metrica-valor"><?= (int) ($resumenGeneral['leads_objeciones'] ?? 0) ?></strong>
            </article>

            <article class="metrica-card">
                <p class="metrica-label">Tareas pendientes</p>
                <strong class="metrica-valor"><?= (int) ($resumenTareas['pendientes'] ?? 0) ?></strong>
            </article>

            <article class="metrica-card metrica-alerta">
                <p class="metrica-label">Tareas retrasadas</p>
                <strong class="metrica-valor"><?= (int) ($resumenTareas['retrasadas'] ?? 0) ?></strong>
            </article>
        </section>

        <section class="bloque">
            <div class="bloque-top">
                <h2>Embudo comercial</h2>
                <p class="bloque-subtexto">Distribución de leads, valor y tiempo medio por etapa.</p>
            </div>

            <div class="pipeline-grid">
                <?php foreach ($resumenPipeline as $estado => $fila): ?>
                    <article class="pipeline-card <?= htmlspecialchars($clasesEstado[(string) $estado] ?? '') ?>">
                        <div class="pipeline-top">
                            <h3><?= htmlspecialchars((string) $estado) ?></h3>
                            <span><?= htmlspecialchars((string) ($fila['porcentaje'] ?? 0)) ?>%</span>
                        </div>

                        <p class="pipeline-total"><?= (int) ($fila['total'] ?? 0) ?> leads</p>
                        <p class="pipeline-valor"><?= htmlspecialchars(dashboard_money_eur((float) ($fila['valor_total'] ?? 0))) ?></p>
                        <p class="pipeline-meta">Media: <?= htmlspecialchars((string) ($fila['media_dias'] ?? 0)) ?> días</p>

                        <div class="pipeline-barra">
                            <div class="pipeline-barra-valor" style="width: <?= htmlspecialchars((string) ($fila['porcentaje'] ?? 0)) ?>%;"></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="dashboard-grid">
            <section class="bloque bloque-columna" id="dashboardObjeciones">
                <div class="bloque-top">
                    <h2>Objeciones</h2>
                    <p class="bloque-subtexto">Bloqueos más repetidos y soluciones más usadas.</p>
                </div>

                <div class="objeciones-resumen">
                    <article class="mini-card">
                        <span>Abiertas</span>
                        <strong><?= (int) ($resumenTareas['objeciones_abiertas'] ?? 0) ?></strong>
                    </article>
                    <article class="mini-card">
                        <span>Resueltas</span>
                        <strong><?= (int) ($resumenTareas['objeciones_resueltas'] ?? 0) ?></strong>
                    </article>
                </div>

                <div class="ranking-bloque">
                    <h3>Tipos de objeción</h3>

                    <?php if (empty($objecionesPorTipo)): ?>
                        <p class="texto-vacio">No hay datos de objeciones para estos filtros.</p>
                    <?php else: ?>
                        <div class="ranking-lista">
                            <?php foreach ($objecionesPorTipo as $fila): ?>
                                <?php
                                $total = (int) ($fila['total'] ?? 0);
                                $ancho = dashboard_porcentaje_barra($total, $maxObjeciones);
                                ?>
                                <article class="ranking-item">
                                    <div class="ranking-head">
                                        <strong><?= htmlspecialchars((string) ($fila['tipo_bloqueo'] ?? 'Definir')) ?></strong>
                                        <span><?= $total ?></span>
                                    </div>
                                    <div class="ranking-barra">
                                        <div class="ranking-barra-valor" style="width: <?= $ancho ?>%;"></div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="ranking-bloque">
                    <h3>Soluciones más usadas</h3>

                    <?php if (empty($solucionesMasUsadas)): ?>
                        <p class="texto-vacio">Aún no hay soluciones definidas.</p>
                    <?php else: ?>
                        <div class="ranking-lista">
                            <?php foreach ($solucionesMasUsadas as $fila): ?>
                                <?php
                                $total = (int) ($fila['total'] ?? 0);
                                $ancho = dashboard_porcentaje_barra($total, $maxSoluciones);
                                ?>
                                <article class="ranking-item">
                                    <div class="ranking-head">
                                        <strong><?= htmlspecialchars((string) ($fila['solucion_bloqueo'] ?? 'Definir')) ?></strong>
                                        <span><?= $total ?></span>
                                    </div>
                                    <div class="ranking-barra azul">
                                        <div class="ranking-barra-valor" style="width: <?= $ancho ?>%;"></div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="bloque bloque-columna">
                <div class="bloque-top">
                    <h2>Seguimientos urgentes</h2>
                    <p class="bloque-subtexto">Tareas activas con vencimiento más cercano.</p>
                </div>

                <?php if (empty($seguimientosUrgentes)): ?>
                    <p class="texto-vacio">No hay seguimientos urgentes para estos filtros.</p>
                <?php else: ?>
                    <div class="lista-operativa">
                        <?php foreach ($seguimientosUrgentes as $item): ?>
                            <article class="item-operativo">
                                <div>
                                    <h3><?= htmlspecialchars((string) ($item['lead_nombre'] ?? 'Lead')) ?></h3>
                                    <p><?= htmlspecialchars((string) ($item['tipo_actividad'] ?? 'Actividad')) ?> · <?= htmlspecialchars((string) ($item['asignado_nombre'] ?? 'Sin asignar')) ?></p>
                                </div>
                                <div class="item-operativo-meta">
                                    <span class="estado-tarea <?= ((string) ($item['estado'] ?? '') === 'Pendiente') ? 'estado-pendiente' : 'estado-curso' ?>">
                                        <?= htmlspecialchars((string) ($item['estado'] ?? '-')) ?>
                                    </span>
                                    <small><?= htmlspecialchars(dashboard_fecha_es((string) ($item['fecha_final'] ?? ''))) ?></small>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </section>

        <section class="bloque">
            <div class="bloque-top">
                <h2>Leads sin contacto reciente</h2>
                <p class="bloque-subtexto">Leads sin último contacto o con seguimiento retrasado.</p>
            </div>

            <div class="tabla-wrap">
                <table class="tabla-dashboard">
                    <thead>
                        <tr>
                            <th>Lead</th>
                            <th>Estado</th>
                            <th>Servicio</th>
                            <th>Responsable</th>
                            <th>Último contacto</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leadsSinContacto)): ?>
                            <tr>
                                <td colspan="6" class="tabla-vacia">No hay leads pendientes de contacto para estos filtros.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leadsSinContacto as $lead): ?>
                                <?php $estadoLead = (string) ($lead['estado'] ?? ''); ?>
                                <tr>
                                    <td>
                                        <a href="<?= BASE_URL . 'leads/' . (int) ($lead['id'] ?? 0) ?>" class="enlace-tabla">
                                            <?= htmlspecialchars((string) ($lead['lead_nombre'] ?? 'Lead')) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="estado-chip <?= htmlspecialchars($clasesEstado[$estadoLead] ?? '') ?>">
                                            <?= htmlspecialchars($estadoLead) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars((string) ($lead['servicios'] ?? '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($lead['responsable_nombre'] ?? 'Sin asignar')) ?></td>
                                    <td><?= !empty($lead['ultimo_contacto']) ? htmlspecialchars(dashboard_fecha_es((string) $lead['ultimo_contacto'])) : 'Sin contacto' ?></td>
                                    <td><?= htmlspecialchars(dashboard_money_eur((float) ($lead['valor'] ?? 0))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?php if ($esAdmin): ?>
            <section class="bloque">
                <div class="bloque-top">
                    <h2>Productividad por usuario</h2>
                    <p class="bloque-subtexto">Comparativa de leads, cierres y valor ganado.</p>
                </div>

                <?php if (empty($resumenUsuarios)): ?>
                    <p class="texto-vacio">No hay datos de usuarios para estos filtros.</p>
                <?php else: ?>
                    <div class="usuarios-grid">
                        <?php foreach ($resumenUsuarios as $fila): ?>
                            <article class="usuario-card">
                                <div class="usuario-card-top">
                                    <h3><?= htmlspecialchars((string) ($fila['nombre'] ?? 'Usuario')) ?></h3>
                                    <span><?= htmlspecialchars((string) ($fila['rol'] ?? 'ventas')) ?></span>
                                </div>

                                <div class="usuario-metricas">
                                    <div>
                                        <small>Leads</small>
                                        <strong><?= (int) ($fila['total_leads'] ?? 0) ?></strong>
                                    </div>
                                    <div>
                                        <small>Ganados</small>
                                        <strong><?= (int) ($fila['ganados'] ?? 0) ?></strong>
                                    </div>
                                    <div>
                                        <small>Conversión</small>
                                        <strong><?= htmlspecialchars((string) ($fila['conversion'] ?? 0)) ?>%</strong>
                                    </div>
                                </div>

                                <p class="usuario-valor"><?= htmlspecialchars(dashboard_money_eur((float) ($fila['valor_ganado'] ?? 0))) ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>