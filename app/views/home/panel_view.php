<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Panel') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <script defer src="<?= BASE_URL ?>js/app.js"></script>
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($titulo ?? 'Panel') ?></h1>

        <?php if (!empty($flash)): ?>
            <div class="flash-message <?= htmlspecialchars($flash['clase'] ?? 'error') ?>">
                <?php if (!empty($flash['icono'])): ?>
                    <span class="flash-icono"><?= htmlspecialchars($flash['icono']) ?></span>
                <?php endif; ?>
                <span><?= htmlspecialchars($flash['mensaje'] ?? '') ?></span>
            </div>
        <?php endif; ?>

        <p>Has iniciado sesión correctamente.</p>

        <?php if (!empty($usuario)): ?>
            <ul>
                <li><strong>ID:</strong> <?= htmlspecialchars((string) $usuario['id']) ?></li>
                <li><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></li>
                <li><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></li>
                <li><strong>Rol:</strong> <?= htmlspecialchars($usuario['rol']) ?></li>
            </ul>
        <?php endif; ?>

        <p>
            <a href="<?= BASE_URL ?>logout">Cerrar sesión</a>
        </p>
    </main>
</body>
</html>
