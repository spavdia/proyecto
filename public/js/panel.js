"use strict";

document.addEventListener('DOMContentLoaded', function () {
    const panel = document.getElementById('panelApp');
    const boton = document.getElementById('botonMenu');
    const aside = document.getElementById('asidePanel');
    const fondo = document.getElementById('fondoMenu');

    if (!panel || !boton || !aside || !fondo) {
        return;
    }

    function abrirMenu() {
        panel.classList.add('menu-abierto');
        boton.setAttribute('aria-expanded', 'true');
    }

    function cerrarMenu() {
        panel.classList.remove('menu-abierto');
        boton.setAttribute('aria-expanded', 'false');
    }

    boton.addEventListener('click', function () {
        if (panel.classList.contains('menu-abierto')) {
            cerrarMenu();
            return;
        }

        abrirMenu();
    });

    fondo.addEventListener('click', cerrarMenu);

    aside.addEventListener('click', function (evento) {
        if (evento.target.closest('a')) {
            cerrarMenu();
        }
    });

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') {
            cerrarMenu();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const panel = document.getElementById('panelApp');

    if (!panel) {
        return;
    }

    panel.addEventListener('change', function (evento) {
        const selector = evento.target.closest('.selector-estado');

        if (!selector) {
            return;
        }

        const formulario = selector.closest('.form-estado');

        if (formulario) {
            formulario.submit();
        }
    });
});
