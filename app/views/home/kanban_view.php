<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Pipeline';
$archivoCssVista = 'kanban.css';
$archivoJsVista = 'kanban.js';
$menuActivo = 'pipeline';

require_once APP_ROOT . '/app/views/layouts/header.php';

/** @var array<string, mixed> $usuario */
/** @var array<string, array<int, array<string, mixed>>> $leadsPorEstado */

$nombreUsuario = (string)($usuario['nombre'] ?? 'Usuario');
$rolUsuario = (string)($usuario['rol'] ?? 'ventas');

$estadosPanel = [
    'Nuevo Lead' => [
        'titulo' => 'Nuevo Lead',
        'clase'  => 'estado-nuevo'
    ],
    'Contactado' => [
        'titulo' => 'Contactado',
        'clase'  => 'estado-contactado'
    ],
    'En Progreso' => [
        'titulo' => 'En Progreso',
        'clase'  => 'estado-progreso'
    ],
    'Objeciones' => [
        'titulo' => 'Objeciones',
        'clase'  => 'estado-objeciones'
    ],
    'Ganado' => [
        'titulo' => 'Ganado',
        'clase'  => 'estado-ganado'
    ],
    'Perdido' => [
        'titulo' => 'Perdido',
        'clase'  => 'estado-perdido'
    ]
];
?>

<div class="panel" id="kanbanApp" data-base-url="<?= htmlspecialchars(BASE_URL) ?>">
    <?php require APP_ROOT . '/app/views/layouts/panel_aside.php'; ?>

    <div class="fondo-menu" id="fondoMenu" aria-hidden="true"></div>

    <main class="contenido">
        <header class="cabecera-kanban">
            <div class="cabecera-info">
                <p class="cabecera-etiqueta">Pipeline interactivo</p>
                <h1>Embudo de ventas</h1>
                <p class="cabecera-texto">
                    Gestiona las tarjetas.
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

        <?php if (!empty($mensajeFlash)): ?>
            <div class="mensaje-flash mensaje-<?= htmlspecialchars((string)($claseFlash ?? 'info')) ?>" role="alert" aria-live="assertive">
                <?php if (!empty($iconoFlash)): ?>
                    <span class="icono-flash" aria-hidden="true"><?= htmlspecialchars((string)$iconoFlash) ?></span>
                <?php endif; ?>
                <span><?= htmlspecialchars((string)$mensajeFlash) ?></span>
            </div>
        <?php endif; ?>

        <section class="kanban-tablero" id="kanbanTablero" aria-label="Tablero kanban del pipeline">
            <?php foreach ($estadosPanel as $estadoClave => $configEstado): ?>
                <?php
                $leadsEstado = $leadsPorEstado[$estadoClave] ?? [];
                $valorTotalEstado = 0.0;

                foreach ($leadsEstado as $leadValor) {
                    $valorTotalEstado += (float)($leadValor['valor'] ?? 0);
                }
                ?>

                <article class="kanban-columna <?= htmlspecialchars($configEstado['clase']) ?>" data-estado="<?= htmlspecialchars($estadoClave) ?>">
                    <header class="kanban-columna-top">
                        <div class="kanban-flecha-wrap">
                            <div class="kanban-flecha">
                                <span><?= htmlspecialchars($configEstado['titulo']) ?></span>
                            </div>
                        </div>

                        <div class="kanban-resumen">
                            <div class="kanban-resumen-item">
                                <strong data-contador><?= count($leadsEstado) ?></strong>
                                <span>Leads</span>
                            </div>

                            <div class="kanban-resumen-item">
                                <strong data-total-valor><?= number_format($valorTotalEstado, 2, ',', '.') ?> €</strong>
                                <span>Valor</span>
                            </div>
                        </div>
                    </header>

                    <div class="kanban-lista">
                        <?php if (empty($leadsEstado)): ?>
                            <div class="kanban-vacio">Sin leads</div>
                        <?php else: ?>
                            <?php foreach ($leadsEstado as $lead): ?>
                                <?php $valorTarjeta = (float)($lead['valor'] ?? 0); ?>

                                <article
                                    class="kanban-tarjeta"
                                    draggable="true"
                                    data-id="<?= (int)($lead['id'] ?? 0) ?>"
                                    data-estado="<?= htmlspecialchars((string)($lead['estado'] ?? '')) ?>"
                                    data-valor="<?= htmlspecialchars((string)$valorTarjeta) ?>">
                                    <div class="kanban-tarjeta-top">
                                        <h3>
                                            <a href="<?= BASE_URL . 'leads/' . (int)($lead['id'] ?? 0) ?>">
                                                <?= htmlspecialchars((string)($lead['lead_nombre'] ?? 'Lead')) ?>
                                            </a>
                                        </h3>
                                        <span class="kanban-chip"><?= htmlspecialchars((string)($lead['servicios'] ?? '-')) ?></span>
                                    </div>

                                    <div class="kanban-meta">
                                        <p><strong>Responsable:</strong> <?= htmlspecialchars((string)($lead['responsable_nombre'] ?? 'Sin asignar')) ?></p>
                                        <p><strong>Teléfono:</strong> <?= htmlspecialchars((string)($lead['telefono'] ?? '-')) ?></p>
                                        <p><strong>Valor:</strong> <?= $valorTarjeta > 0 ? htmlspecialchars(number_format($valorTarjeta, 2, ',', '.')) . ' €' : '-' ?></p>
                                    </div>

                                    <div class="kanban-acciones">
                                        <a href="<?= BASE_URL . 'leads/' . (int)($lead['id'] ?? 0) ?>" class="enlace-detalle">Ver detalle</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <div class="mensaje-kanban" id="mensajeKanban" aria-live="polite"></div>
    </main>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>