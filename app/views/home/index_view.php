<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Inicio') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <script defer src="<?= BASE_URL ?>js/app.js"></script>
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($titulo ?? 'Inicio') ?></h1>

        <?php if (!empty($flash)): ?>
            <div class="flash-message <?= htmlspecialchars($flash['clase'] ?? 'error') ?>">
                <?php if (!empty($flash['icono'])): ?>
                    <span class="flash-icono"><?= htmlspecialchars($flash['icono']) ?></span>
                <?php endif; ?>
                <span><?= htmlspecialchars($flash['mensaje'] ?? '') ?></span>
            </div>
        <?php endif; ?>

        <p><?= htmlspecialchars($mensaje ?? '') ?></p>

        <hr>

        <h2>Estado actual del proyecto</h2>

        <ul>
            <li>Fase 0 operativa</li>
            <li>Router funcionando</li>
            <li>HomeController funcionando</li>
            <li>Vista cargando correctamente</li>
            <li>Estructura base lista para evolucionar a CRM Pipeline</li>
        </ul>

        <p>Siguiente objetivo: construir autenticación de usuarios y base de datos inicial.</p>
        <p><a href="<?= BASE_URL ?>login">Ir al login</a></p>
        <a href="<?= BASE_URL . 'panel' ?>">Ir al panel</a>
    </main>
</body>
</html>
