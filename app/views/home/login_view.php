<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Login') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/login.css">
    <script defer src="<?= BASE_URL ?>js/app.js"></script>
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($titulo ?? 'Login') ?></h1>

        <?php if (!empty($flash)): ?>
            <div class="flash-message <?= htmlspecialchars($flash['clase'] ?? 'error') ?>">
                <?php if (!empty($flash['icono'])): ?>
                    <span class="flash-icono"><?= htmlspecialchars($flash['icono']) ?></span>
                <?php endif; ?>
                <span><?= htmlspecialchars($flash['mensaje'] ?? '') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>login" method="POST">
            <div>
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>

            <div>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>
