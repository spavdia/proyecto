<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Listado';
$archivoCssVista = 'listado.css';
$archivoJsVista = 'listado.js';
$menuActivo = 'listado';

$__ctx = get_defined_vars();

$usuario = (isset($__ctx['usuario']) && is_array($__ctx['usuario'])) ? $__ctx['usuario'] : [];
$leadListados = (isset($__ctx['leadListados']) && is_array($__ctx['leadListados'])) ? $__ctx['leadListados'] : [];
$filtros = (isset($__ctx['filtros']) && is_array($__ctx['filtros'])) ? $__ctx['filtros'] : [];
$usuariosLista = (isset($__ctx['usuariosLista']) && is_array($__ctx['usuariosLista'])) ? $__ctx['usuariosLista'] : [];
$serviciosLista = (isset($__ctx['serviciosLista']) && is_array($__ctx['serviciosLista'])) ? $__ctx['serviciosLista'] : [];
$estadosLista = (isset($__ctx['estadosLista']) && is_array($__ctx['estadosLista'])) ? $__ctx['estadosLista'] : [];

$mensajeFlash = isset($__ctx['mensajeFlash']) ? $__ctx['mensajeFlash'] : null;
$iconoFlash = isset($__ctx['iconoFlash']) ? $__ctx['iconoFlash'] : null;
$claseFlash = isset($__ctx['claseFlash']) ? (string) $__ctx['claseFlash'] : 'info';

$nombreUsuario = (string) ($usuario['nombre'] ?? 'Usuario');
$rolUsuario = (string) ($usuario['rol'] ?? 'ventas');
$esAdmin = (($usuario['rol'] ?? '') === 'admin');

if (!function_exists('listado_fecha_es')) {
    function listado_fecha_es(?string $fecha): string
    {
        if (empty($fecha)) {
            return 'Sin fecha';
        }

        $timestamp = strtotime($fecha);
        return $timestamp ? date('d/m/Y', $timestamp) : (string) $fecha;
    }
}

if (!function_exists('listado_estado_class')) {
    function listado_estado_class(?string $estado): string
    {
        $estadoNormalizado = strtolower(trim((string) $estado));
        $estadoNormalizado = str_replace([' ', 'á', 'é', 'í', 'ó', 'ú'], ['-', 'a', 'e', 'i', 'o', 'u'], $estadoNormalizado);

        return match ($estadoNormalizado) {
            'nuevo-lead' => 'estado-nuevo',
            'contactado' => 'estado-contactado',
            'en-progreso' => 'estado-progreso',
            'objeciones' => 'estado-objeciones',
            'ganado' => 'estado-ganado',
            'perdido' => 'estado-perdido',
            default => 'estado-nuevo'
        };
    }
}

require_once APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel listado-page" id="listadoApp">
    <?php require APP_ROOT . '/app/views/layouts/panel_aside.php'; ?>

    <div class="fondo-menu" id="fondoMenu" aria-hidden="true"></div>

    <main class="contenido listado-contenido">
        <header class="cabecera-listado">
            <div class="cabecera-listado-info">
                <p class="cabecera-etiqueta">CRM comercial</p>
                <h1>Listado de Leads</h1>
                <p class="cabecera-texto">
                    Vista general de leads del CRM con los mismos filtros del dashboard.
                </p>
            </div>

            <div class="cabecera-listado-acciones">
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

                <a href="<?= BASE_URL . 'panel' ?>" class="boton boton-secundario">Volver al panel</a>

                <?php require APP_ROOT . '/app/views/layouts/theme_toggle.php'; ?>

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

        <section class="bloque-listado bloque-filtros-listado">
            <div class="bloque-listado-top">
                <div>
                    <h2>Filtros</h2>
                    <p>Usa los filtros del dashboard para acotar el listado.</p>
                </div>
            </div>

            <form action="<?= BASE_URL . 'leads/listado' ?>" method="GET" class="form-filtros">
                <?php if ($esAdmin): ?>
                    <div class="campo">
                        <label for="usuario_id">Usuario</label>
                        <select class="" id="usuario_id" name="usuario_id">
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
                    <select class=""id="servicios" name="servicios">
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
                    <select class="" id="estado" name="estado">
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
                    <select class="" id="origen" name="origen">
                        <option value="">Todos</option>
                        <option value="formulario_web" <?= (($filtros['origen'] ?? '') === 'formulario_web') ? 'selected' : '' ?>>Web</option>
                        <option value="app_interna" <?= (($filtros['origen'] ?? '') === 'app_interna') ? 'selected' : '' ?>>Interna</option>
                    </select>
                </div>

                <div class="acciones-filtros">
                    <button type="submit" class="boton boton-principal">Aplicar filtros</button>
                    <a href="<?= BASE_URL . 'leads/listado' ?>" class="boton boton-volver">Limpiar</a>
                </div>
            </form>
        </section>

        <section class="bloque-listado bloque-tabla-listado">
            <div class="bloque-listado-top">
                <div>
                    <h2>Leads encontrados</h2>
                    <p><?= count($leadListados) ?> lead<?= count($leadListados) === 1 ? '' : 's' ?> en el listado actual.</p>
                </div>
            </div>

            <div class="tabla-listado-wrap">
                <table class="tabla-listado">
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
                        <?php if (empty($leadListados)): ?>
                            <tr>
                                <td colspan="8" class="tabla-vacia">No hay leads para los filtros seleccionados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leadListados as $lead): ?>
                                <tr>
                                    <td class="celda-lead">
                                        <a href="<?= BASE_URL . 'leads/' . (int) ($lead['id'] ?? 0) ?>" class="enlace-lead">
                                            <?= htmlspecialchars((string) ($lead['lead_nombre'] ?? '')) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars((string) ($lead['responsable_nombre'] ?? 'Sin asignar')) ?></td>
                                    <td>
                                        <span class="estado-chip <?= htmlspecialchars(listado_estado_class((string) ($lead['estado'] ?? ''))) ?>">
                                            <?= htmlspecialchars((string) ($lead['estado'] ?? '')) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars((string) ($lead['servicios'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) ($lead['telefono'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string) ($lead['indicaciones'] ?? '')) ?></td>
                                    <td><?= !empty($lead['ultimo_contacto']) ? htmlspecialchars(listado_fecha_es((string) $lead['ultimo_contacto'])) : 'Sin contacto' ?></td>
                                    <td><?= (($lead['origen'] ?? '') === 'formulario_web') ? 'Web' : 'Interna' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>