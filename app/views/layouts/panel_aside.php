<?php

$menuActivo = $menuActivo ?? '';
?>

<aside class="aside" id="asidePanel" aria-label="Menú lateral">
    <div class="aside-top">
        <a href="<?= BASE_URL ?>" class="marca" aria-label="Ir al inicio de PipelineDesk">
            <img src="<?= BASE_URL . 'img/logo-crm.png' ?>" alt="Logo de PipelineDesk">
            <span>PipelineDesk</span>
        </a>
    </div>

    <nav class="menu" aria-label="Navegación principal del panel">
        <p class="menu-titulo">General</p>
        <a href="<?= BASE_URL . 'panel' ?>" class="menu-enlace <?= $menuActivo === 'panel' ? 'activo' : '' ?>">Panel</a>
        <a href="<?= BASE_URL . 'pipeline' ?>" class="menu-enlace <?= $menuActivo === 'pipeline' ? 'activo' : '' ?>">Pipeline / kanban</a>
        <a href="<?= BASE_URL . 'dashboard' ?>" class="menu-enlace <?= $menuActivo === 'dashboard' ? 'activo' : '' ?>">Dashboard</a>
        

        <p class="menu-titulo">Comercial</p>
        <a href="<?= BASE_URL . 'leads/nuevo' ?>" class="menu-enlace <?= $menuActivo === 'nuevo_lead' ? 'activo' : '' ?>">Nuevo lead</a>
        <a href="<?= BASE_URL . 'leads/listado' ?>" class="menu-enlace <?= $menuActivo === 'listado' ? 'activo' : '' ?>">Listados</a>
        <a href="<?= BASE_URL . 'tareas' ?>" class="menu-enlace <?= $menuActivo === 'tareas' ? 'activo' : '' ?>">Tareas</a>

        <p class="menu-titulo">Configuración</p>
        <a href="#" class="menu-enlace">Usuarios</a>
        <a href="#" class="menu-enlace">Definir negocio</a>
        <a href="<?= BASE_URL . 'logout' ?>" class="menu-enlace">Cerrar sesión</a>
    </nav>
</aside>