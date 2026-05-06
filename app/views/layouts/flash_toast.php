<?php
$mensajeFlash = $mensajeFlash ?? null;
$claseFlash = $claseFlash ?? 'info';

if (empty($mensajeFlash)) {
    return;
}

$tipoToast = 'info';
$iconoSvg = '';

switch ($claseFlash) {
    case 'exito':
        $tipoToast = 'success';
        $iconoSvg = '
            <svg class="toast-icon-svg" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
            </svg>
        ';
        break;

    case 'error':
        $tipoToast = 'danger';
        $iconoSvg = '
            <svg class="toast-icon-svg" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
            </svg>
        ';
        break;

    case 'warning':
        $tipoToast = 'warning';
        $iconoSvg = '
            <svg class="toast-icon-svg" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        ';
        break;

    default:
        $tipoToast = 'info';
        $iconoSvg = '
            <svg class="toast-icon-svg" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8h.01M11 12h1v4h1m9-4a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        ';
        break;
}
?>

<div class="toast-stack" id="toastStack">
    <div
        id="appToast"
        class="app-toast app-toast-<?= htmlspecialchars($tipoToast) ?>"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        data-toast
        data-toast-delay="4500">

        <div class="app-toast-icono" aria-hidden="true">
            <?= $iconoSvg ?>
        </div>

        <div class="app-toast-contenido">
            <p class="app-toast-texto"><?= htmlspecialchars((string) $mensajeFlash) ?></p>
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
</div>