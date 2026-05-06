<?php

declare(strict_types=1);
?>
<?php require APP_ROOT . '/app/views/layouts/flash_toast.php'; ?>

<?php if (!empty($archivoJsVista)): ?>
    <script src="<?= BASE_URL . 'js/' . htmlspecialchars($archivoJsVista) ?>"></script>
<?php endif; ?>
<script src="<?= BASE_URL ?>js/theme.js"></script>
<script src="<?= BASE_URL ?>js/flash-toast.js"></script>
</body>

</html>