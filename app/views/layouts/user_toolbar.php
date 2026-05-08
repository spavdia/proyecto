<?php
$usuarioToolbar = (isset($usuario) && is_array($usuario)) ? $usuario : [];

$nombreUsuarioToolbar = (string) ($usuarioToolbar['nombre'] ?? 'Usuario');
$rolUsuarioToolbar = (string) ($usuarioToolbar['rol'] ?? 'ventas');
?>

<div class="usuario-toolbar">
    <div class="usuario-toolbar-meta">
        <span class="usuario-nombre"><?= htmlspecialchars($nombreUsuarioToolbar) ?></span>
        <span class="usuario-rol"><?= htmlspecialchars($rolUsuarioToolbar) ?></span>
    </div>

    <div class="usuario-toolbar-acciones">
        <button
            type="button"
            class="boton-icono-toolbar"
            data-theme-toggle
            aria-pressed="false"
            title="Cambiar tema"
            aria-label="Cambiar tema">
            <span class="icono-theme" data-theme-label-light aria-hidden="true">🌙</span>
            <span class="icono-theme" data-theme-label-dark hidden aria-hidden="true">☀️</span>
        </button>

        <a
            href="<?= BASE_URL . 'tareas' ?>"
            class="boton-tareas-toolbar"
            title="Ver tareas"
            aria-label="Ver tareas">
            Ver tareas
        </a>

        <a
            href="<?= BASE_URL . 'logout' ?>"
            class="boton-icono-toolbar boton-logout-toolbar"
            title="Cerrar sesión"
            aria-label="Cerrar sesión">
            <span aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M15 3h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-3"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </a>
    </div>
</div>
