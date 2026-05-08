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

$clasesPrioridadTarjeta = [
    'Alta' => 'tarjeta-alta',
    'Media' => 'tarjeta-media',
    'Baja' => 'tarjeta-baja'
];

$clasesPrioridadCampo = [
    'Alta' => 'prioridad-alta',
    'Media' => 'prioridad-media',
    'Baja' => 'prioridad-baja'
];
?>

<div class="panel" id="kanbanApp" data-base-url="<?= htmlspecialchars(BASE_URL) ?>">
    <?php require APP_ROOT . '/app/views/layouts/panel_aside.php'; ?>

    <div class="fondo-menu" id="fondoMenu" aria-hidden="true"></div>

    <main class="contenido">
        <header class="cabecera-kanban">
            <div class="cabecera-info">
                <p class="cabecera-etiqueta">CRM PipelineDesk</p>
                <h1>Etapas del negocio</h1>
                <p class="cabecera-texto">
                   Mueve tus leads por fases del proceso, evita cuellos de botella
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
                    class="boton-config"
                    id="botonConfigKanban"
                    aria-expanded="false"
                    aria-controls="panelConfigKanban"
                    aria-label="Configurar campos visibles de las tarjetas">
                    <span aria-hidden="true">⚙</span>
                </button>
                <?php require APP_ROOT . '/app/views/layouts/theme_toggle.php'; ?>

                <div class="usuario">
                    <span class="usuario-nombre"><?= htmlspecialchars($nombreUsuario) ?></span>
                    <span class="usuario-rol"><?= htmlspecialchars($rolUsuario) ?></span>
                </div>
            </div>

            <div class="panel-config" id="panelConfigKanban" hidden>
                <div class="panel-config-top">
                    <h2>Campos visibles</h2>
                    <p>Nombre y Servicio son obligatorios.</p>
                </div>

                <div class="config-grid">
                    <label class="config-item obligatorio">
                        <input type="checkbox" data-campo-config="lead_nombre" checked disabled>
                        <span>Nombre</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="email">
                        <span>Email</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="telefono" checked>
                        <span>Teléfono</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="responsable_nombre" checked>
                        <span>Responsable</span>
                    </label>

                    <label class="config-item obligatorio">
                        <input type="checkbox" data-campo-config="servicios" checked disabled>
                        <span>Servicio</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="valor" checked>
                        <span>Valor</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="prioridad" checked>
                        <span>Prioridad</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="estado">
                        <span>Estado</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="indicaciones">
                        <span>Indicaciones</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="ultimo_contacto">
                        <span>Último contacto</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="origen">
                        <span>Origen</span>
                    </label>

                    <label class="config-item">
                        <input type="checkbox" data-campo-config="lead_score">
                        <span>Score</span>
                    </label>
                </div>
            </div>
        </header>

       

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
                                <?php
                                $valorTarjeta = (float)($lead['valor'] ?? 0);
                                $ultimoContacto = !empty($lead['ultimo_contacto']) ? (string)$lead['ultimo_contacto'] : 'Sin contacto';
                                $origenTexto = (($lead['origen'] ?? '') === 'formulario_web') ? 'Formulario web' : 'App interna';
                                $prioridadLead = (string)($lead['prioridad'] ?? 'Media');
                                $claseTarjeta = $clasesPrioridadTarjeta[$prioridadLead] ?? 'tarjeta-media';
                                $clasePrioridad = $clasesPrioridadCampo[$prioridadLead] ?? 'prioridad-media';
                                ?>

                                <article
                                    class="kanban-tarjeta <?= htmlspecialchars($claseTarjeta) ?>"
                                    draggable="true"
                                    data-id="<?= (int)($lead['id'] ?? 0) ?>"
                                    data-estado="<?= htmlspecialchars((string)($lead['estado'] ?? '')) ?>"
                                    data-valor="<?= htmlspecialchars((string)$valorTarjeta) ?>">

                                    <div class="kanban-tarjeta-top">
                                        <div class="campo-tarjeta campo-obligatorio" data-campo="lead_nombre">
                                            <h3>
                                                <a href="<?= BASE_URL . 'leads/' . (int)($lead['id'] ?? 0) ?>">
                                                    <?= htmlspecialchars((string)($lead['lead_nombre'] ?? 'Lead')) ?>
                                                </a>
                                            </h3>
                                        </div>

                                        <div class="campo-tarjeta campo-obligatorio" data-campo="servicios">
                                            <span class="kanban-chip"><?= htmlspecialchars((string)($lead['servicios'] ?? '-')) ?></span>
                                        </div>
                                    </div>

                                    <div class="kanban-meta">
                                        <p class="campo-tarjeta" data-campo="email">
                                            <strong>Email:</strong>
                                            <?= htmlspecialchars((string)($lead['email'] ?? '-')) ?>
                                        </p>

                                        <p class="campo-tarjeta" data-campo="telefono">
                                            <strong>Teléfono:</strong>
                                            <?= htmlspecialchars((string)($lead['telefono'] ?? '-')) ?>
                                        </p>

                                        <p class="campo-tarjeta" data-campo="responsable_nombre">
                                            <strong>Responsable:</strong>
                                            <?= htmlspecialchars((string)($lead['responsable_nombre'] ?? 'Sin asignar')) ?>
                                        </p>

                                        <p class="campo-tarjeta" data-campo="valor">
                                            <strong>Valor:</strong>
                                            <?= $valorTarjeta > 0 ? htmlspecialchars(number_format($valorTarjeta, 2, ',', '.')) . ' €' : '-' ?>
                                        </p>

                                        <p class="campo-tarjeta" data-campo="prioridad">
                                            <strong>Prioridad:</strong>
                                            <span class="chip-prioridad <?= htmlspecialchars($clasePrioridad) ?>">
                                                <?= htmlspecialchars($prioridadLead) ?>
                                            </span>
                                        </p>

                                        <p class="campo-tarjeta" data-campo="estado">
                                            <strong>Estado:</strong>
                                            <?= htmlspecialchars((string)($lead['estado'] ?? '-')) ?>
                                        </p>

                                        <p class="campo-tarjeta" data-campo="indicaciones">
                                            <strong>Indicaciones:</strong>
                                            <?= !empty($lead['indicaciones'])
                                                ? htmlspecialchars(mb_strimwidth((string)$lead['indicaciones'], 0, 70, '...'))
                                                : '-' ?>
                                        </p>

                                        <p class="campo-tarjeta" data-campo="ultimo_contacto">
                                            <strong>Último contacto:</strong>
                                            <?= htmlspecialchars($ultimoContacto) ?>
                                        </p>

                                        <p class="campo-tarjeta" data-campo="origen">
                                            <strong>Origen:</strong>
                                            <?= htmlspecialchars($origenTexto) ?>
                                        </p>

                                        <p class="campo-tarjeta" data-campo="lead_score">
                                            <strong>Score:</strong>
                                            <?= htmlspecialchars((string)($lead['lead_score'] ?? '0')) ?>
                                        </p>
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