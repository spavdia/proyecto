<?php
declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Panel';
$archivoCssVista = 'panel.css';
$archivoJsVista = 'panel.js';

require_once APP_ROOT . '/app/views/layouts/header.php';

$nombreUsuario = $usuario['nombre'] ?? 'Usuario';
$rolUsuario = $usuario['rol'] ?? 'ventas';

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
?>

<div class="panel" id="panelApp">
    <aside class="aside" id="asidePanel" aria-label="Menú lateral">
        <div class="aside-top">
            <a href="<?= BASE_URL ?>" class="marca" aria-label="Ir al inicio de PipelineDesk">
                <img src="<?= BASE_URL . 'img/logo-crm.png' ?>" alt="Logo de PipelineDesk">
                <span>PipelineDesk</span>
            </a>
        </div>

        <nav class="menu" aria-label="Navegación principal del panel">
            <p class="menu-titulo">General</p>
            <a href="<?= BASE_URL . 'panel' ?>" class="menu-enlace activo">Panel</a>
            <a href="#" class="menu-enlace">Dashboard</a>
            <a href="#" class="menu-enlace">Calendario</a>

            <p class="menu-titulo">Comercial</p>
            <a href="<?= BASE_URL . 'leads/nuevo' ?>" class="menu-enlace">Nuevo lead</a>
            <a href="#" class="menu-enlace">Pipeline</a>
            <a href="#" class="menu-enlace">Notas</a>
            <a href="#" class="menu-enlace">Tareas</a>
            <a href="#" class="menu-enlace">Objeciones</a>

            <p class="menu-titulo">Configuración</p>
            <a href="#" class="menu-enlace">Usuarios</a>
            <a href="#" class="menu-enlace">Estadísticas</a>
            <a href="<?= BASE_URL . 'logout' ?>" class="menu-enlace">Cerrar sesión</a>
        </nav>
    </aside>

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
                    aria-label="Abrir menú"
                >
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

        <?php if (!empty($mensajeFlash)): ?>
            <div class="mensaje-flash mensaje-<?= htmlspecialchars($claseFlash ?? 'info') ?>" role="alert" aria-live="assertive">
                <?php if (!empty($iconoFlash)): ?>
                    <span class="icono-flash" aria-hidden="true"><?= htmlspecialchars($iconoFlash) ?></span>
                <?php endif; ?>
                <span><?= htmlspecialchars($mensajeFlash) ?></span>
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
                                <th>Servicio</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Prioridad</th>
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
                                    <tr>
                                        <td><?= htmlspecialchars($lead['lead_nombre'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($lead['responsable_nombre'] ?? 'Sin asignar') ?></td>
                                        <td><?= htmlspecialchars($lead['servicios'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($lead['email'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($lead['telefono'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($lead['prioridad'] ?? 'Media') ?></td>
                                        <td>
                                            <?= !empty($lead['ultimo_contacto'])
                                                ? htmlspecialchars($lead['ultimo_contacto'])
                                                : 'Sin contacto'; ?>
                                        </td>
                                        <td><?= htmlspecialchars($lead['origen'] ?? '') ?></td>
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