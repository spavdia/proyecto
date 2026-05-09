document.addEventListener('DOMContentLoaded', function () {
    const app = document.querySelector('#dashboardApp');
    const botonMenu = document.querySelector('#botonMenu');
    const fondoMenu = document.querySelector('#fondoMenu');

    if (!app || !botonMenu || !fondoMenu) {
        return;
    }

    botonMenu.addEventListener('click', function () {
        const abierto = app.classList.toggle('menu-abierto');
        botonMenu.setAttribute('aria-expanded', abierto ? 'true' : 'false');
    });

    fondoMenu.addEventListener('click', function () {
        app.classList.remove('menu-abierto');
        botonMenu.setAttribute('aria-expanded', 'false');
    });

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') {
            app.classList.remove('menu-abierto');
            botonMenu.setAttribute('aria-expanded', 'false');
        }
    });
});
