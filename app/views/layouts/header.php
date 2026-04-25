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
    <link rel="stylesheet" href="<?= BASE_URL . 'css/style.css' ?>">

    <?php if (!empty($archivoCssVista)): ?>
        <link rel="stylesheet" href="<?= BASE_URL . 'css/' . htmlspecialchars($archivoCssVista) ?>">
    <?php endif; ?>
    
</head>
<body class="cuerpo-pagina">