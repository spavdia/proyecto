<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'PipelineDesk') ?></title>
    <meta name="description" content="PipelineDesk CRM">
    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const useDark = saved ? saved === 'dark' : systemDark;

            if (useDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/tailwind.css">
    <link rel="stylesheet" href="<?= BASE_URL . 'css/style.css' ?>">
    <?php if (!empty($archivoCssVista)): ?>
        <link rel="stylesheet" href="<?= BASE_URL . 'css/' . htmlspecialchars($archivoCssVista) ?>">
    <?php endif; ?>

</head>

<body class="cuerpo-pagina">
    <?php if (!empty($mostrarBotonMenu)): ?>
        <button
            type="button"
            class="boton-menu boton-menu-layout"
            id="botonMenu"
            aria-controls="asidePanel"
            aria-expanded="false"
            aria-label="Abrir menú">
            <span></span>
            <span></span>
            <span></span>
        </button>
    <?php endif; ?>