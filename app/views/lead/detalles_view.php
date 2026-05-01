<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Detalle del lead';
$archivoCssVista = 'detalles.css';
$archivoJsVista = 'detalles.js';
$menuActivo = 'panel';
$mostrarBotonMenu = false;

require_once APP_ROOT . '/app/views/layouts/header.php';

/** @var array<string, mixed> $usuario */
/** @var array<string, mixed> $lead */
/** @var array<int, array<string, mixed>> $notas */
/** @var array<int, array<string, mixed>> $historial */
/** @var array<int, string> $estadosEmbudo */
/** @var array<int, string> $estadosLista */
/** @var array<int, string> $prioridades */
/** @var array<int, array<string, mixed>> $responsables */
/** @var array<int, string> $serviciosLista */

$esModoEdicion = $esModoEdicion ?? false;
$erroresEditar = $erroresEditar ?? [];
$leadForm = $leadForm ?? [];
$esAdmin = (($usuario['rol'] ?? '') === 'admin');

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
$leadScore = (string)($lead['lead_score'] ?? '0');
$ultimoContacto = (string)($lead['ultimo_contacto'] ?? '');
$origenLead = (string)($lead['origen'] ?? '');
$indicacionesLead = (string)($lead['indicaciones'] ?? '');
$leadId = (int)($lead['id'] ?? 0);

$ultimoContactoInput = '';
if ($ultimoContacto !== '') {
    $ultimoContactoInput = date('Y-m-d\TH:i', strtotime($ultimoContacto));
}

$createdAtInput = '';
if ($fechaCreacion !== '') {
    $createdAtInput = date('Y-m-d\TH:i', strtotime($fechaCreacion));
}

$clasesEstado = [
    'Nuevo Lead' => 'estado-nuevo',
    'Contactado' => 'estado-contactado',
    'En Progreso' => 'estado-progreso',
    'Objeciones' => 'estado-objeciones',
    'Ganado'     => 'estado-ganado',
    'Perdido'    => 'estado-perdido'
];

$claseEstadoActual = $clasesEstado[$estadoActual] ?? 'estado-nuevo';

$valorForm = [
    'lead_nombre' => $leadForm['lead_nombre'] ?? $nombreLead,
    'email' => $leadForm['email'] ?? $emailLead,
    'telefono' => $leadForm['telefono'] ?? $telefonoLead,
    'servicios' => $leadForm['servicios'] ?? $servicioLead,
    'valor' => $leadForm['valor'] ?? $valorLead,
    'prioridad' => $leadForm['prioridad'] ?? $prioridadLead,
    'estado' => $leadForm['estado'] ?? $estadoActual,
    'responsable_id' => (int)($leadForm['responsable_id'] ?? ($lead['responsable_id'] ?? 0)),
    'lead_score' => $leadForm['lead_score'] ?? $leadScore,
    'ultimo_contacto' => $leadForm['ultimo_contacto'] ?? $ultimoContactoInput,
    'indicaciones' => $leadForm['indicaciones'] ?? $indicacionesLead,
    'origen' => $leadForm['origen'] ?? $origenLead,
    'created_at' => $leadForm['created_at'] ?? $createdAtInput
];

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

<div class="panel" id="panelApp">
    <?php require APP_ROOT . '/app/views/layouts/panel_aside.php'; ?>

    <div class="fondo-menu" id="fondoMenu" aria-hidden="true"></div>

    <main class="contenido">
        <div class="detalle-lead">
            <div class="detalle-contenido">
                <section class="cabecera-detalle">
                    <div class="cabecera-superior">
                        <div>
                            <p class="cabecera-etiqueta">Detalles del lead</p>
                            <h1><?= htmlspecialchars($nombreLead) ?></h1>
                            <p class="cabecera-meta">
                                Creado el <strong><?= htmlspecialchars($fechaCreacion ?: 'Sin fecha') ?></strong>
                                · Lleva <strong><?= htmlspecialchars((string)$diasEnPanel) ?> días</strong> en el panel
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
                            <?php if (($usuario['rol'] ?? '') === 'admin'): ?> <form action="<?= BASE_URL . 'leads/' . $leadId . '/eliminar' ?>" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este lead?');">
                                    <button type="submit" class="boton boton-perdido">Eliminar</button>
                                </form>
                            <?php
                            endif; ?>
                        </div>
                    

                    <div class="cabecera-etapas">
                        <div class="etapas-top">
                            <h2>Etapas del acuerdo</h2>
                            <span class="estado-actual <?= htmlspecialchars($claseEstadoActual) ?>">
                                <?= htmlspecialchars($estadoActual) ?>
                            </span>
                        </div>

                        <?php
                        $indiceEstadoActual = array_search($estadoActual, $estadosEmbudo, true);
                        ?>

                        <div class="barra-etapas" aria-label="Estado actual del lead en el embudo">
                            <?php foreach ($estadosEmbudo as $indice => $estadoItem): ?>
                                <?php
                                $clasePaso = 'paso pendiente';

                                if ($indiceEstadoActual !== false) {
                                    if ($indice < $indiceEstadoActual) {
                                        $clasePaso = 'paso completado';
                                    } elseif ($indice === $indiceEstadoActual) {
                                        $clasePaso = 'paso activo';
                                    }
                                }
                                ?>
                                <div class="<?= $clasePaso ?>">
                                    <span><?= htmlspecialchars($estadoItem) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <section class="detalle-grid">
                    <!-- INFO DEL LEAD -->
                    <aside class="tarjeta info-lead <?= $esModoEdicion ? 'modo-edicion' : '' ?>" id="infoLead">
                        <div class="tarjeta-top tarjeta-top-acciones">
                            <h2>Info. del lead</h2>

                            <?php if ($esModoEdicion): ?>
                                <div class="acciones-info">
                                    <a href="<?= BASE_URL . 'leads/' . $leadId ?>" class="boton boton-secundario">Cancelar</a>
                                </div>
                            <?php else: ?>
                                <div class="acciones-info">
                                    <a href="<?= BASE_URL . 'leads/' . $leadId . '?editar=1#infoLead' ?>" class="boton boton-principal">Editar</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($esModoEdicion): ?>
                            <form action="<?= BASE_URL . 'leads/' . $leadId . '/actualizar' ?>" method="POST" class="form-editar-lead">
                                <div class="info-lista">
                                    <div class="campo">
                                        <label for="lead_nombre">Nombre del lead</label>
                                        <input type="text" id="lead_nombre" name="lead_nombre" value="<?= htmlspecialchars((string)$valorForm['lead_nombre']) ?>">
                                        <span class="error-campo"><?= !empty($erroresEditar['lead_nombre']) ? htmlspecialchars($erroresEditar['lead_nombre']) : '' ?></span>
                                    </div>

                                    <div class="campo">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" value="<?= htmlspecialchars((string)$valorForm['email']) ?>">
                                        <span class="error-campo"><?= !empty($erroresEditar['email']) ? htmlspecialchars($erroresEditar['email']) : '' ?></span>
                                    </div>

                                    <div class="campo">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars((string)$valorForm['telefono']) ?>">
                                        <span class="error-campo"><?= !empty($erroresEditar['telefono']) ? htmlspecialchars($erroresEditar['telefono']) : '' ?></span>
                                    </div>

                                    <div class="campo">
                                        <label for="responsable_id">Responsable</label>
                                        <select id="responsable_id" name="responsable_id">
                                            <?php foreach ($responsables as $responsable): ?>
                                                <option value="<?= (int)$responsable['id'] ?>" <?= ((int)$valorForm['responsable_id'] === (int)$responsable['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars((string)$responsable['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="error-campo"><?= !empty($erroresEditar['responsable_id']) ? htmlspecialchars($erroresEditar['responsable_id']) : '' ?></span>
                                    </div>

                                    <div class="campo">
                                        <label for="servicios">Servicio</label>
                                        <select id="servicios" name="servicios">
                                            <option value="">Selecciona un servicio</option>
                                            <?php foreach ($serviciosLista as $servicio): ?>
                                                <option value="<?= htmlspecialchars($servicio) ?>" <?= ($valorForm['servicios'] === $servicio) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($servicio) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="error-campo"><?= !empty($erroresEditar['servicios']) ? htmlspecialchars($erroresEditar['servicios']) : '' ?></span>
                                    </div>

                                    <div class="campo">
                                        <label for="valor">Valor del acuerdo</label>
                                        <input type="text" id="valor" name="valor" value="<?= htmlspecialchars((string)$valorForm['valor']) ?>">
                                        <span class="error-campo"><?= !empty($erroresEditar['valor']) ? htmlspecialchars($erroresEditar['valor']) : '' ?></span>
                                    </div>

                                    <div class="campo">
                                        <label for="prioridad">Prioridad</label>
                                        <select id="prioridad" name="prioridad">
                                            <?php foreach ($prioridades as $prioridadItem): ?>
                                                <option value="<?= htmlspecialchars($prioridadItem) ?>" <?= ($valorForm['prioridad'] === $prioridadItem) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($prioridadItem) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="error-campo"><?= !empty($erroresEditar['prioridad']) ? htmlspecialchars($erroresEditar['prioridad']) : '' ?></span>
                                    </div>

                                    <div class="campo">
                                        <label for="estado">Estado</label>
                                        <select id="estado" name="estado">
                                            <?php foreach ($estadosLista as $estadoItem): ?>
                                                <option value="<?= htmlspecialchars($estadoItem) ?>" <?= ($valorForm['estado'] === $estadoItem) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($estadoItem) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="error-campo"><?= !empty($erroresEditar['estado']) ? htmlspecialchars($erroresEditar['estado']) : '' ?></span>
                                    </div>

                                    <?php if ($esAdmin): ?>
                                        <div class="campo">
                                            <label for="lead_score">Lead Score</label>
                                            <input type="number" min="0" step="1" id="lead_score" name="lead_score" value="<?= htmlspecialchars((string)$valorForm['lead_score']) ?>">
                                            <span class="error-campo"><?= !empty($erroresEditar['lead_score']) ? htmlspecialchars($erroresEditar['lead_score']) : '' ?></span>
                                        </div>

                                        <div class="campo">
                                            <label for="ultimo_contacto">Último contacto</label>
                                            <input type="datetime-local" id="ultimo_contacto" name="ultimo_contacto" value="<?= htmlspecialchars((string)$valorForm['ultimo_contacto']) ?>">
                                            <span class="error-campo"><?= !empty($erroresEditar['ultimo_contacto']) ? htmlspecialchars($erroresEditar['ultimo_contacto']) : '' ?></span>
                                        </div>

                                        <div class="campo campo-completo">
                                            <label for="indicaciones">Indicaciones</label>
                                            <textarea id="indicaciones" name="indicaciones" rows="5"><?= htmlspecialchars((string)$valorForm['indicaciones']) ?></textarea>
                                            <span class="error-campo"><?= !empty($erroresEditar['indicaciones']) ? htmlspecialchars($erroresEditar['indicaciones']) : '' ?></span>
                                        </div>

                                        <div class="campo">
                                            <label for="origen">Origen</label>
                                            <select id="origen" name="origen">
                                                <option value="formulario_web" <?= ($valorForm['origen'] === 'formulario_web') ? 'selected' : '' ?>>Formulario web</option>
                                                <option value="app_interna" <?= ($valorForm['origen'] === 'app_interna') ? 'selected' : '' ?>>App interna</option>
                                            </select>
                                            <span class="error-campo"><?= !empty($erroresEditar['origen']) ? htmlspecialchars($erroresEditar['origen']) : '' ?></span>
                                        </div>

                                        <div class="campo">
                                            <label for="created_at">Fecha de creación</label>
                                            <input type="datetime-local" id="created_at" name="created_at" value="<?= htmlspecialchars((string)$valorForm['created_at']) ?>">
                                            <span class="error-campo"><?= !empty($erroresEditar['created_at']) ? htmlspecialchars($erroresEditar['created_at']) : '' ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="info-item">
                                            <p class="info-label">Lead Score</p>
                                            <div class="info-valor"><?= htmlspecialchars($leadScore ?: '0') ?></div>
                                        </div>

                                        <div class="info-item">
                                            <p class="info-label">Último contacto</p>
                                            <div class="info-valor"><?= htmlspecialchars($ultimoContacto ?: 'Sin contacto') ?></div>
                                        </div>

                                        <div class="info-item campo-completo">
                                            <p class="info-label">Indicaciones</p>
                                            <div class="info-valor info-texto"><?= nl2br(htmlspecialchars($indicacionesLead ?: '-')) ?></div>
                                        </div>

                                        <div class="info-item">
                                            <p class="info-label">Origen</p>
                                            <div class="info-valor"><?= htmlspecialchars($origenLead ?: '-') ?></div>
                                        </div>

                                        <div class="info-item">
                                            <p class="info-label">Fecha de creación</p>
                                            <div class="info-valor"><?= htmlspecialchars($fechaCreacion ?: '-') ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="acciones-guardar">
                                    <button type="submit" class="boton boton-principal">Guardar</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="info-lista">
                                <div class="info-item">
                                    <p class="info-label">Nombre de contacto</p>
                                    <div class="info-valor"><?= htmlspecialchars($nombreLead ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Email</p>
                                    <div class="info-valor"><?= htmlspecialchars($emailLead ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Teléfono</p>
                                    <div class="info-valor"><?= htmlspecialchars($telefonoLead ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Responsable</p>
                                    <div class="info-valor"><?= htmlspecialchars($responsableLead ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Servicio</p>
                                    <div class="info-valor"><?= htmlspecialchars($servicioLead ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Lead Score</p>
                                    <div class="info-valor"><?= htmlspecialchars($leadScore ?: '0') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Valor del acuerdo</p>
                                    <div class="info-valor"><?= htmlspecialchars($valorLead ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Prioridad</p>
                                    <div class="info-valor"><?= htmlspecialchars($prioridadLead ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Estado actual</p>
                                    <div class="info-valor"><?= htmlspecialchars($estadoActual ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Último contacto</p>
                                    <div class="info-valor"><?= htmlspecialchars($ultimoContacto ?: 'Sin contacto') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Origen</p>
                                    <div class="info-valor"><?= htmlspecialchars($origenLead ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Fecha de creación</p>
                                    <div class="info-valor"><?= htmlspecialchars($fechaCreacion ?: '-') ?></div>
                                </div>
                                <div class="info-item">
                                    <p class="info-label">Indicaciones</p>
                                    <div class="info-valor info-texto"><?= nl2br(htmlspecialchars($indicacionesLead ?: '-')) ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </aside>

                    <div class="columna-actividad">
                        <section class="tarjeta tarjeta-nota">
                            <div class="tarjeta-top">
                                <h2>Actividad & Email</h2>
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
                                <h2>Historial del lead</h2>
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
            </div>
        </div>
    </main>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>