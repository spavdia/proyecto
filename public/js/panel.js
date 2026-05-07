"use strict";

document.addEventListener('DOMContentLoaded', function () {
    const panelApp = document.getElementById('panelApp');
    const botonMenu = document.getElementById('botonMenu');
    const fondoMenu = document.getElementById('fondoMenu');

    if (botonMenu && panelApp) {
        botonMenu.addEventListener('click', function () {
            const abierto = panelApp.classList.toggle('menu-abierto');
            botonMenu.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        });
    }

    if (fondoMenu && panelApp && botonMenu) {
        fondoMenu.addEventListener('click', function () {
            panelApp.classList.remove('menu-abierto');
            botonMenu.setAttribute('aria-expanded', 'false');
        });
    }

    if (panelApp) {
        panelApp.addEventListener('change', function (evento) {
            const selector = evento.target.closest('.selector-estado');

            if (!selector) {
                return;
            }

            const formulario = selector.closest('.form-estado');

            if (formulario) {
                formulario.submit();
            }
        });
    }

    document.addEventListener('click', function (evento) {
        const botonCerrar = evento.target.closest('[data-cerrar-notificacion]');

        if (!botonCerrar) {
            return;
        }

        const notificacion = botonCerrar.closest('.notificacion-panel');

        if (notificacion) {
            notificacion.remove();
        }
    });
});