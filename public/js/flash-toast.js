document.addEventListener('DOMContentLoaded', function () {
    const toasts = document.querySelectorAll('[data-toast]');

    toasts.forEach(function (toast) {
        const closeButton = toast.querySelector('[data-toast-close]');
        const delay = parseInt(toast.getAttribute('data-toast-delay') || '4500', 10);

        requestAnimationFrame(function () {
            toast.classList.add('app-toast-visible');
        });

        let hideTimeout = setTimeout(function () {
            cerrarToast(toast);
        }, delay);

        function cerrarToast(elemento) {
            if (!elemento || elemento.dataset.cerrando === '1') {
                return;
            }

            elemento.dataset.cerrando = '1';
            elemento.classList.remove('app-toast-visible');
            elemento.classList.add('app-toast-saliendo');

            setTimeout(function () {
                if (elemento.parentNode) {
                    elemento.parentNode.removeChild(elemento);
                }
            }, 260);
        }

        if (closeButton) {
            closeButton.addEventListener('click', function () {
                clearTimeout(hideTimeout);
                cerrarToast(toast);
            });
        }

        toast.addEventListener('mouseenter', function () {
            clearTimeout(hideTimeout);
        });

        toast.addEventListener('mouseleave', function () {
            hideTimeout = setTimeout(function () {
                cerrarToast(toast);
            }, 1800);
        });
    });
});