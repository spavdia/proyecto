<?php

use Sergio\Lib\SessionManager;
use Sergio\App\models\LeadModel;

$toastItems = [];

$sessionToasts = SessionManager::getMensajesFlash(true);
if (!empty($sessionToasts)) {
    foreach ($sessionToasts as $toast) {
        if (!is_array($toast) || empty($toast['mensaje'])) {
            continue;
        }

        $toastItems[] = [
            'mensaje' => (string) $toast['mensaje'],
            'tipo' => (string) ($toast['clase'] ?? 'info')
        ];
    }
}

$usuarioToast = SessionManager::get('usuario');
$usuarioToast = is_array($usuarioToast) ? $usuarioToast : [];
$usuarioToastId = (int) ($usuarioToast['id'] ?? 0);

if ($usuarioToastId > 0) {
    $leadToastModel = new LeadModel();
    $logrosToast = $leadToastModel->getPendingGanadoNotificationsByUsuario($usuarioToastId);

    if (!empty($logrosToast)) {
        $imagenesUsuarios = [
            1 => 'user1.png',
            2 => 'user2.png',
            3 => 'user3.png'
        ];

        $idsLeidos = [];

        foreach ($logrosToast as $logroToast) {
            $usuarioOrigenId = (int) ($logroToast['usuario_origen_id'] ?? 0);
            $idsLeidos[] = (int) ($logroToast['id'] ?? 0);

            $toastItems[] = [
                'mensaje' => (string) ($logroToast['mensaje'] ?? ''),
                'tipo' => 'exito',
                'rich' => true,
                'titulo' => (string) ($logroToast['titulo'] ?? 'FELICIDADES'),
                'imagen' => (string) ($imagenesUsuarios[$usuarioOrigenId] ?? 'user1.png'),
                'origen_nombre' => (string) ($logroToast['origen_nombre'] ?? 'Equipo comercial'),
                'enlace' => (string) (($logroToast['enlace'] ?? '') !== '' ? $logroToast['enlace'] : ('leads/' . (int) ($logroToast['lead_id'] ?? 0))),
                'cta' => 'Ver detalles'
            ];
        }

        $leadToastModel->markNotificationsAsReadByIds($idsLeidos);
    }
}

if (!empty($toasts) && is_array($toasts)) {
    foreach ($toasts as $toast) {
        if (!is_array($toast) || empty($toast['mensaje'])) {
            continue;
        }

        $toastItems[] = [
            'mensaje' => (string) $toast['mensaje'],
            'tipo' => (string) ($toast['tipo'] ?? 'info')
        ];
    }
}

if (empty($toastItems) && !empty($mensajeFlash)) {
    $toastItems[] = [
        'mensaje' => (string) $mensajeFlash,
        'tipo' => (string) ($claseFlash ?? 'info')
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
        'exito' => 'success',
        'error' => 'danger',
        'warning' => 'warning',
        default => 'info',
    };
}
?>

<div class="toast-stack" id="toastStack">
    <?php foreach ($toastItems as $indice => $toast): ?>
        <?php
        $tipo = toastTipoCss((string) ($toast['tipo'] ?? 'info'));
        $toastId = 'appToast-' . $indice . '-' . uniqid();
        $esRich = !empty($toast['rich']);
        ?>

        <?php if ($esRich): ?>
            <div
                id="<?= htmlspecialchars($toastId) ?>"
                class="app-toast app-toast-<?= htmlspecialchars($tipo) ?> app-toast-rich app-toast-premio"
                role="alert"
                aria-live="assertive"
                aria-atomic="true"
                data-toast
                data-toast-delay="6500">

                <div class="app-toast-premio-cabecera">
                    <span class="app-toast-premio-badge">🎉 Felicidades</span>
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

                <div class="app-toast-premio-cuerpo">
                    <img
                        class="app-toast-premio-avatar"
                        src="<?= BASE_URL . 'img/' . htmlspecialchars((string) ($toast['imagen'] ?? 'user1.png')) ?>"
                        alt="Imagen de <?= htmlspecialchars((string) ($toast['origen_nombre'] ?? 'Usuario')) ?>">

                    <div class="app-toast-premio-info">
                        <p class="app-toast-premio-titulo"><?= htmlspecialchars((string) ($toast['titulo'] ?? '¡Felicidades!')) ?></p>
                        <p class="app-toast-premio-usuario"><?= htmlspecialchars((string) ($toast['origen_nombre'] ?? 'Equipo comercial')) ?></p>
                        <p class="app-toast-premio-texto"><?= htmlspecialchars((string) ($toast['mensaje'] ?? 'Se ha conseguido un nuevo negocio.')) ?></p>
                    </div>
                </div>

                <?php if (!empty($toast['enlace'])): ?>
                    <div class="app-toast-premio-acciones">
                        <a class="app-toast-premio-enlace" href="<?= BASE_URL . ltrim((string) $toast['enlace'], '/') ?>">
                            <?= htmlspecialchars((string) ($toast['cta'] ?? 'Ver detalles')) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div
                id="<?= htmlspecialchars($toastId) ?>"
                class="app-toast app-toast-<?= htmlspecialchars($tipo) ?>"
                role="alert"
                aria-live="assertive"
                aria-atomic="true"
                data-toast
                data-toast-delay="4500">

                <div class="app-toast-icono" aria-hidden="true">
                    <?= toastIconoSvg((string) ($toast['tipo'] ?? 'info')) ?>
                </div>

                <div class="app-toast-contenido">
                    <p class="app-toast-texto"><?= htmlspecialchars((string) ($toast['mensaje'] ?? '')) ?></p>
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
        <?php endif; ?>
    <?php endforeach; ?>
</div>
