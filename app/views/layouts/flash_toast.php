<?php

use Sergio\Lib\SessionManager;

$toastItems = [];

/*
|--------------------------------------------------------------------------
| 1. Toasts guardados en sesión
|--------------------------------------------------------------------------
*/
$sessionToasts = SessionManager::getMensajesFlash(true);

if (!empty($sessionToasts)) {
    foreach ($sessionToasts as $toast) {
        if (!is_array($toast) || empty($toast['mensaje'])) {
            continue;
        }

        $toastItems[] = [
            'mensaje' => (string) $toast['mensaje'],
            'tipo'    => (string) ($toast['clase'] ?? 'info')
        ];
    }
}

/*
|--------------------------------------------------------------------------
| 2. Toasts extra enviados manualmente desde la vista/controller
|--------------------------------------------------------------------------
*/
if (!empty($toasts) && is_array($toasts)) {
    foreach ($toasts as $toast) {
        if (!is_array($toast) || empty($toast['mensaje'])) {
            continue;
        }

        $toastItems[] = [
            'mensaje' => (string) $toast['mensaje'],
            'tipo'    => (string) ($toast['tipo'] ?? 'info')
        ];
    }
}

/*
|--------------------------------------------------------------------------
| 3. Fallback por compatibilidad:
|    si no hay cola en sesión ni array de toasts, usamos mensajeFlash
|--------------------------------------------------------------------------
*/
if (empty($toastItems) && !empty($mensajeFlash)) {
    $toastItems[] = [
        'mensaje' => (string) $mensajeFlash,
        'tipo'    => (string) ($claseFlash ?? 'info')
    ];
}

if (empty($toastItems)) {
    return;
}

function toastIconoSvg(string $tipo): string
{
    switch ($tipo) {
        case 'exito':
            return '<svg class="toast-icon-svg" aria-hidden="true" viewBox="0 0 24 24" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/></svg>';

        case 'error':
            return '<svg class="toast-icon-svg" aria-hidden="true" viewBox="0 0 24 24" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>';

        case 'warning':
            return '<svg class="toast-icon-svg" aria-hidden="true" viewBox="0 0 24 24" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>';

        default:
            return '<svg class="toast-icon-svg" aria-hidden="true" viewBox="0 0 24 24" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8h.01M11 12h1v4h1m9-4a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>';
    }
}

function toastTipoCss(string $tipo): string
{
    return match ($tipo) {
        'exito'   => 'success',
        'error'   => 'danger',
        'warning' => 'warning',
        default   => 'info',
    };
}
?>

<div class="toast-stack" id="toastStack">
    <?php foreach ($toastItems as $indice => $toast): ?>
        <?php
        $tipo = toastTipoCss($toast['tipo']);
        $toastId = 'appToast-' . $indice . '-' . uniqid();
        ?>
        <div
            id="<?= htmlspecialchars($toastId) ?>"
            class="app-toast app-toast-<?= htmlspecialchars($tipo) ?>"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
            data-toast
            data-toast-delay="4500">

            <div class="app-toast-icono" aria-hidden="true">
                <?= toastIconoSvg($toast['tipo']) ?>
            </div>

            <div class="app-toast-contenido">
                <p class="app-toast-texto"><?= htmlspecialchars($toast['mensaje']) ?></p>
            </div>

            <button
                type="button"
                class="app-toast-cerrar"
                data-toast-close
                aria-label="Cerrar notificación">
                <span class="sr-only">Cerrar</span>
                <svg class="toast-close-svg" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                </svg>
            </button>
        </div>
    <?php endforeach; ?>
</div>