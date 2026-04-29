<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Detalle del lead';
$archivoCssVista = 'detalles.css';
$archivoJsVista = null;

require_once APP_ROOT . '/app/views/layouts/header.php';

/** @var array<string, mixed> $lead */
/** @var array<int, array<string, mixed>> $notas */
/** @var array<int, array<string, mixed>> $historial */
/** @var array<int, string> $estadosEmbudo */

$estadoActual = (string)($lead['estado'] ?? ESTADO_POR_DEFECTO);
$fechaCreacion = (string)($lead['created_at'] ?? '');
$diasEnPanel = (int)($diasEnPanel ?? 0);

$nombreLead = (string)($lead['lead_nombre'] ?? '');
$emailLead = (string)($lead['email'] ?? '');
$telefonoLead = (string)($lead['telefono'] ?? '');
$responsableLead = (string)($lead['responsable_nombre'] ?? 'Sin asignar');
$servicioLead = (string)($lead['servicios'] ?? '');
$valorLead = (string)($lead['valor'] ?? '');
$prioridadLead = (string)($lead['prioridad'] ?? '');
$ultimoContacto = (string)($lead['ultimo_contacto'] ?? 'Sin contacto');
$origenLead = (string)($lead['origen'] ?? '');
$indicacionesLead = (string)($lead['indicaciones'] ?? '');
$leadId = (int)($lead['id'] ?? 0);

$clasesEstado = [
    'Nuevo Lead' => 'estado-nuevo',
    'Contactado' => 'estado-contactado',
    'En Progreso' => 'estado-progreso',
    'Objeciones' => 'estado-objeciones',
    'Ganado'     => 'estado-ganado',
    'Perdido'    => 'estado-perdido'
];

$claseEstadoActual = $clasesEstado[$estadoActual] ?? 'estado-nuevo';
?>

<div class="detalle-lead">
    <main class="detalle-contenido">
        <section class="cabecera-detalle">
            <div class="cabecera-superior">
                <div>
                    <p class="cabecera-etiqueta">Detalle del lead</p>
                    <h1><?= htmlspecialchars($nombreLead) ?></h1>
                    <p class="cabecera-meta">
                        Creado el <strong><?= htmlspecialchars($fechaCreacion ?: 'Sin fecha') ?></strong>
                        · Lleva <strong><?= htmlspecialchars((string)$diasEnPanel) ?> días</strong> en el panel
                    </p>
                </div>

                <div class="cabecera-acciones">
                    <form action="<?= BASE_URL . 'leads/cambiar-estado/' . $leadId ?>" method="POST">
                        <input type="hidden" name="estado" value="Ganado">
                        <input type="hidden" name="volver_detalle" value="1">
                        <button type="submit" class="boton boton-ganado">Ganado</button>
                    </form>

                    <form action="<?= BASE_URL . 'leads/cambiar-estado/' . $leadId ?>" method="POST">
                        <input type="hidden" name="estado" value="Perdido">
                        <input type="hidden" name="volver_detalle" value="1">
                        <button type="submit" class="boton boton-perdido">Perdido</button>
                    </form>
                </div>
            </div>

            <div class="cabecera-etapas">
                <div class="etapas-top">
                    <h2>Etapas del acuerdo</h2>
                    <span class="estado-actual <?= htmlspecialchars($claseEstadoActual) ?>">
                        <?= htmlspecialchars($estadoActual) ?>
                    </span>
                </div>

                <div class="barra-etapas" aria-label="Estado actual del lead en el embudo">
                    <?php foreach ($estadosEmbudo as $estadoItem): ?>
                        <?php $clasePaso = ($estadoItem === $estadoActual) ? 'paso activo' : 'paso'; ?>
                        <div class="<?= $clasePaso ?>">
                            <span><?= htmlspecialchars($estadoItem) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="detalle-grid">
            <aside class="tarjeta info-lead">
                <div class="tarjeta-top">
                    <h2>Info. del lead</h2>
                </div>

                <div class="info-lista">
                    <div class="info-item"><p class="info-label">Nombre de contacto</p><div class="info-valor"><?= htmlspecialchars($nombreLead ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Email</p><div class="info-valor"><?= htmlspecialchars($emailLead ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Teléfono</p><div class="info-valor"><?= htmlspecialchars($telefonoLead ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Responsable</p><div class="info-valor"><?= htmlspecialchars($responsableLead ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Servicio</p><div class="info-valor"><?= htmlspecialchars($servicioLead ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Valor del acuerdo</p><div class="info-valor"><?= htmlspecialchars($valorLead ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Prioridad</p><div class="info-valor"><?= htmlspecialchars($prioridadLead ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Estado actual</p><div class="info-valor"><?= htmlspecialchars($estadoActual ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Último contacto</p><div class="info-valor"><?= htmlspecialchars($ultimoContacto ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Origen</p><div class="info-valor"><?= htmlspecialchars($origenLead ?: '-') ?></div></div>
                    <div class="info-item"><p class="info-label">Indicaciones</p><div class="info-valor info-texto"><?= nl2br(htmlspecialchars($indicacionesLead ?: '-')) ?></div></div>
                </div>
            </aside>

            <div class="columna-actividad">
                <section class="tarjeta tarjeta-nota">
                    <div class="tarjeta-top">
                        <h2>Agregar actividad</h2>
                    </div>

                    <?php if (!empty($mensajeFlash)): ?>
                        <div class="mensaje-flash mensaje-<?= htmlspecialchars($claseFlash ?? 'info') ?>" role="alert" aria-live="assertive">
                            <?php if (!empty($iconoFlash)): ?>
                                <span class="icono-flash" aria-hidden="true"><?= htmlspecialchars($iconoFlash) ?></span>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($mensajeFlash) ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL . 'leads/' . $leadId . '/notas/guardar' ?>" method="POST" class="form-nota">
                        <div class="campo">
                            <label for="tipo_actividad">Actividad</label>
                            <select name="tipo_actividad" id="tipo_actividad">
                                <option value="">Selecciona una actividad</option>
                                <option value="Llamada">Llamada</option>
                                <option value="Email">Email</option>
                                <option value="Cita presencial">Cita presencial</option>
                            </select>
                        </div>

                        <div class="campo">
                            <label for="contenido_nota">Nota</label>
                            <textarea name="contenido" id="contenido_nota" rows="5" placeholder="Escribe el resumen de la actividad realizada..."></textarea>
                        </div>

                        <div class="acciones-formulario">
                            <button type="submit" class="boton boton-principal">Guardar actividad</button>
                        </div>
                    </form>
                </section>

                <section class="tarjeta tarjeta-historial">
                    <div class="tarjeta-top">
                        <h2>Historial y actividades</h2>
                    </div>

                    <?php if (empty($historial)): ?>
                        <p class="historial-vacio">Aún no hay movimientos registrados para este lead.</p>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($historial as $evento): ?>
                                <?php
                                $tipoEvento = (string)($evento['tipo_evento'] ?? '');
                                $iconoEvento = $tipoEvento === 'nota' ? '📝' : ($tipoEvento === 'cambio_estado' ? '🔁' : '⭐');
                                ?>
                                <article class="timeline-item">
                                    <div class="timeline-icono"><?= $iconoEvento ?></div>
                                    <div class="timeline-contenido">
                                        <p class="timeline-titulo">
                                            <?= htmlspecialchars((string)($evento['titulo'] ?? 'Movimiento')) ?>
                                        </p>
                                        <p class="timeline-texto">
                                            <?= nl2br(htmlspecialchars((string)($evento['descripcion'] ?? ''))) ?>
                                        </p>

                                        <?php if (!empty($evento['estado_anterior']) || !empty($evento['estado_nuevo'])): ?>
                                            <p class="timeline-texto">
                                                Estado anterior: <strong><?= htmlspecialchars((string)($evento['estado_anterior'] ?? '-')) ?></strong>
                                                · Estado nuevo: <strong><?= htmlspecialchars((string)($evento['estado_nuevo'] ?? '-')) ?></strong>
                                            </p>
                                        <?php endif; ?>

                                        <p class="timeline-meta">
                                            <?= htmlspecialchars((string)($evento['usuario_nombre'] ?? 'Sistema')) ?>
                                            ·
                                            <?= htmlspecialchars((string)($evento['created_at'] ?? '')) ?>
                                        </p>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </section>
    </main>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
