"use strict";

document.addEventListener('DOMContentLoaded', function () {
    const app = document.querySelector('#listadoApp');
    const botonMenu = document.querySelector('#botonMenu');
    const aside = document.querySelector('#asidePanel');
    const fondoMenu = document.querySelector('#fondoMenu');

    if (!app || !botonMenu || !aside || !fondoMenu) {
        return;
    }

    function abrirMenu() {
        app.classList.add('menu-abierto');
        botonMenu.setAttribute('aria-expanded', 'true');
    }

    function cerrarMenu() {
        app.classList.remove('menu-abierto');
        botonMenu.setAttribute('aria-expanded', 'false');
    }

    botonMenu.addEventListener('click', function () {
        if (app.classList.contains('menu-abierto')) {
            cerrarMenu();
            return;
        }

        abrirMenu();
    });

    fondoMenu.addEventListener('click', cerrarMenu);

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
