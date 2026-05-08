<?php

declare(strict_types=1);

$tituloPagina = 'PipelineDesk | Nuevo lead';
$archivoCssVista = 'lead_create.css';
$archivoJsVista = 'panel.js';
$menuActivo = 'nuevo_lead';
$mostrarBotonMenu = true;

$errores = $errores ?? [];
$serviciosLista = (isset($serviciosLista) && is_array($serviciosLista)) ? $serviciosLista : [];
$responsables = (isset($responsables) && is_array($responsables)) ? $responsables : [];
$estadosLista = (isset($estadosLista) && is_array($estadosLista)) ? $estadosLista : [];
$prioridades = (isset($prioridades) && is_array($prioridades)) ? $prioridades : [];

require_once APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel" id="panelApp">
    <?php require APP_ROOT . '/app/views/layouts/panel_aside.php'; ?>

    <div class="fondo-menu" id="fondoMenu" aria-hidden="true"></div>

    <main class="contenido">
        <header class="cabecera-lead-panel">
            <div class="cabecera-info">
                <p class="cabecera-etiqueta">CRM Pipeline</p>
                <h1>Nuevo lead</h1>
                <p class="cabecera-texto">
                    Registra una nueva oportunidad comercial
                </p>
            </div>

            <div class="cabecera-acciones">
                <a href="<?= BASE_URL . 'panel' ?>" class="boton boton-secundario">Volver al panel</a>
            </div>
        </header>

        <section class="nuevo-lead">
            <div class="caja-lead">
                

                <form class="formulario-lead" action="<?= BASE_URL . 'leads/guardar' ?>" method="POST" novalidate>
                    <div class="campo">
                        <label for="lead_nombre">Nombre del lead</label>
                        <input
                            type="text"
                            id="lead_nombre"
                            name="lead_nombre"
                            placeholder="Nombre del cliente"
                            value="<?= htmlspecialchars($lead_nombre ?? '') ?>"
                            required>
                        <span class="error-campo">
                            <?= !empty($errores['lead_nombre']) ? htmlspecialchars($errores['lead_nombre']) : '' ?>
                        </span>
                    </div>

                    <div class="campo">
                        <label for="email">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="correo@cliente.com"
                            value="<?= htmlspecialchars($email ?? '') ?>">
                        <span class="error-campo">
                            <?= !empty($errores['email']) ? htmlspecialchars($errores['email']) : '' ?>
                        </span>
                    </div>

                    <div class="campo">
                        <label for="telefono">Teléfono</label>
                        <input
                            type="text"
                            id="telefono"
                            name="telefono"
                            placeholder="Teléfono del cliente"
                            value="<?= htmlspecialchars($telefono ?? '') ?>">
                        <span class="error-campo">
                            <?= !empty($errores['telefono']) ? htmlspecialchars($errores['telefono']) : '' ?>
                        </span>
                    </div>

                    <div class="campo">
                        <label for="servicios">Servicio</label>
                        <select id="servicios" name="servicios" required>
                            <option value="">Selecciona un servicio</option>
                            <?php foreach ($serviciosLista as $servicio): ?>
                                <option
                                    value="<?= htmlspecialchars($servicio) ?>"
                                    <?= (($servicios ?? '') === $servicio) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($servicio) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-campo">
                            <?= !empty($errores['servicios']) ? htmlspecialchars($errores['servicios']) : '' ?>
                        </span>
                    </div>

                    <div class="campo">
                        <label for="responsable_id">Responsable</label>
                        <select id="responsable_id" name="responsable_id">
                            <option value="">Selecciona un responsable</option>
                            <?php foreach ($responsables as $responsable): ?>
                                <option
                                    value="<?= (int)$responsable['id'] ?>"
                                    <?= ((int)($responsable_id ?? USUARIO_POR_DEFECTO) === (int)$responsable['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($responsable['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-campo">
                            <?= !empty($errores['responsable_id']) ? htmlspecialchars($errores['responsable_id']) : '' ?>
                        </span>
                    </div>

                    <div class="campo">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <?php foreach ($estadosLista as $item): ?>
                                <option
                                    value="<?= htmlspecialchars($item) ?>"
                                    <?= (($estado ?? 'Nuevo Lead') === $item) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-campo">
                            <?= !empty($errores['estado']) ? htmlspecialchars($errores['estado']) : '' ?>
                        </span>
                    </div>

                    <div class="campo">
                        <label for="prioridad">Prioridad</label>
                        <select id="prioridad" name="prioridad">
                            <?php foreach ($prioridades as $item): ?>
                                <option
                                    value="<?= htmlspecialchars($item) ?>"
                                    <?= (($prioridad ?? 'Media') === $item) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-campo">
                            <?= !empty($errores['prioridad']) ? htmlspecialchars($errores['prioridad']) : '' ?>
                        </span>
                    </div>

                    <div class="campo">
                        <label for="valor">Valor estimado</label>
                        <input
                            type="text"
                            id="valor"
                            name="valor"
                            placeholder="Ejemplo: 2500"
                            value="<?= htmlspecialchars($valor ?? '') ?>">
                        <span class="error-campo">
                            <?= !empty($errores['valor']) ? htmlspecialchars($errores['valor']) : '' ?>
                        </span>
                    </div>

                    <div class="campo">
                        <label for="indicaciones">Indicaciones</label>
                        <textarea
                            id="indicaciones"
                            name="indicaciones"
                            rows="5"
                            placeholder="Añade información relevante"><?= htmlspecialchars($indicaciones ?? '') ?></textarea>
                        <span class="error-campo">
                            <?= !empty($errores['indicaciones']) ? htmlspecialchars($errores['indicaciones']) : '' ?>
                        </span>
                    </div>

                    <div class="acciones-formulario">
                        <button type="submit" class="boton boton-primario boton-guardar">Guardar lead</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
