<?php

declare(strict_types=1);

use Sergio\Lib\SessionManager;

$usuarioFooter = SessionManager::get('usuario');
$usuarioFooter = is_array($usuarioFooter) ? $usuarioFooter : [];
$estaLogueadoFooter = !empty($usuarioFooter);
?>

<link rel="stylesheet" href="<?= BASE_URL ?>css/footer.css">

<footer class="footer-app">
    <div class="footer-contenedor">
        <div class="footer-grid">
            <section class="footer-bloque">
                <h2 class="footer-titulo">Acceso rápido</h2>
                <ul class="footer-lista">
                    <li><a href="<?= BASE_URL ?>">Inicio</a></li>
                    <li><a href="<?= BASE_URL . 'panel' ?>">Panel</a></li>
                    <li><a href="<?= BASE_URL . 'pipeline' ?>">Pipeline</a></li>
                    <li><a href="<?= BASE_URL . 'dashboard' ?>">Dashboard</a></li>
                </ul>
            </section>

            <section class="footer-bloque">
                <h2 class="footer-titulo">Gestión comercial</h2>
                <ul class="footer-lista">
                    <li><a href="<?= BASE_URL . 'leads/nuevo' ?>">Nuevo lead</a></li>
                    <li><a href="<?= BASE_URL . 'leads/listado' ?>">Listado de leads</a></li>
                    <li><a href="<?= BASE_URL . 'tareas' ?>">Tareas</a></li>
                    <li><a href="<?= BASE_URL . 'contacto' ?>">Contacto</a></li>
                </ul>
            </section>

            <section class="footer-bloque">
                <h2 class="footer-titulo">Soporte y empresa</h2>
                <ul class="footer-lista">
                    <li><span class="footer-dato">Producto:</span> Pipeline CRM</li>
                    <li><span class="footer-dato">Email:</span> soporte@pipelinecrm.com</li>
                    <li><span class="footer-dato">Horario:</span> L-V · 09:00 a 18:00</li>
                    <li><a href="<?= BASE_URL . 'politica-privacidad' ?>">Política de privacidad</a></li>
                </ul>
            </section>

            <section class="footer-bloque">
                <h2 class="footer-titulo">Tecnologías</h2>
                <ul class="footer-lista">
                    <li><span class="footer-dato">Arquitectura:</span> PHP MVC</li>
                    <li><span class="footer-dato">Interfaz:</span> Tailwind + CSS </li>
                    <li><span class="footer-dato">Frontend</span> JavaScript</li>
                    <li>
                        <?php if ($estaLogueadoFooter): ?>
                            <a href="<?= BASE_URL . 'logout' ?>">Cerrar sesión</a>
                        <?php else: ?>
                            <a href="<?= BASE_URL . 'login' ?>">Iniciar sesión</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </section>
        </div>

        <div class="footer-legal">
            <p>© <?= date('Y') ?> Pipeline CRM. Todos los derechos reservados.</p>
            <p>Sergio Pavón Díaz · Proyecto 2ºDAW</p>
        </div>
    </div>
</footer>

<?php require APP_ROOT . '/app/views/layouts/flash_toast.php'; ?>
<script src="<?= BASE_URL ?>js/flash-toast.js"></script>
<?php if (!empty($archivoJsVista)): ?>
    <script src="<?= BASE_URL . 'js/' . htmlspecialchars($archivoJsVista) ?>"></script>
<?php endif; ?>
<script src="<?= BASE_URL ?>js/theme.js"></script>

</body>
</html>