"use strict";
//boton hamburguesa
document.addEventListener('DOMContentLoaded', function () {
    const panel = document.querySelector('#panelApp');
    const boton = document.querySelector('#botonMenu');
    const aside = document.querySelector('#asidePanel');
    const fondo = document.querySelector('#fondoMenu');

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
        } else {
            abrirMenu();
        }
    });

    fondo.addEventListener('click', cerrarMenu);

    aside.addEventListener('click', function (evento) {
        if (evento.target.closest('a') && window.innerWidth <= 768) {
            cerrarMenu();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            cerrarMenu();
        }
    });
});

//cambiar estado con Selector, haciendo submit del form
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