document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('panelApp') || document.querySelector('.panel');
    const botonMenu = document.getElementById('botonMenu');
    const fondoMenu = document.getElementById('fondoMenu');

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