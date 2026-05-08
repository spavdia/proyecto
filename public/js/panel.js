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

    let selectorAbierto = null;
    let menuFlotante = null;

    function cerrarSelectorActual() {
        if (!selectorAbierto) {
            return;
        }

        const trigger = selectorAbierto.querySelector('[data-selector-trigger]');
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        }

        selectorAbierto.classList.remove('abierto');
        selectorAbierto = null;

        if (menuFlotante && menuFlotante.parentNode) {
            menuFlotante.parentNode.removeChild(menuFlotante);
        }
        menuFlotante = null;
    }

    function posicionarMenu(trigger, menu) {
        const rect = trigger.getBoundingClientRect();
        const margen = 8;

        menu.style.visibility = 'hidden';
        menu.style.left = '0px';
        menu.style.top = '0px';
        menu.style.minWidth = rect.width + 'px';

        document.body.appendChild(menu);

        const menuRect = menu.getBoundingClientRect();
        const espacioAbajo = window.innerHeight - rect.bottom - margen;
        const espacioArriba = rect.top - margen;

        let top = rect.bottom + margen;
        if (espacioAbajo < menuRect.height && espacioArriba > menuRect.height) {
            top = rect.top - menuRect.height - margen;
        }

        let left = rect.left;
        if (left + menuRect.width > window.innerWidth - margen) {
            left = Math.max(margen, window.innerWidth - menuRect.width - margen);
        }

        menu.style.left = `${left}px`;
        menu.style.top = `${Math.max(margen, top)}px`;
        menu.style.visibility = 'visible';
    }

    function crearMenuFlotante(selector) {
        const template = selector.querySelector('[data-selector-template]');
        const trigger = selector.querySelector('[data-selector-trigger]');

        if (!template || !trigger) {
            return null;
        }

        const menu = document.createElement('div');
        menu.className = 'selector-estado-flyout';
        menu.setAttribute('role', 'listbox');
        menu.innerHTML = template.innerHTML;

        const input = selector.querySelector('[data-selector-input]');
        const valorActual = input ? input.value : '';

        menu.querySelectorAll('[data-estado]').forEach(function (opcion) {
            const valor = opcion.getAttribute('data-estado') || '';
            if (valor === valorActual) {
                opcion.classList.add('activa');
            }

            opcion.addEventListener('click', function () {
                if (!input) {
                    return;
                }

                input.value = valor;

                const triggerTexto = trigger.querySelector('.selector-estado-trigger-texto');
                if (triggerTexto) {
                    triggerTexto.textContent = valor;
                }

                trigger.className = 'selector-estado-trigger';
                opcion.classList.forEach(function (clase) {
                    if (clase.indexOf('estado-') === 0) {
                        trigger.classList.add(clase);
                    }
                });

                cerrarSelectorActual();

                const formulario = selector.closest('.form-estado');
                if (formulario) {
                    formulario.submit();
                }
            });
        });

        return menu;
    }

    document.querySelectorAll('[data-selector-estado]').forEach(function (selector) {
        const trigger = selector.querySelector('[data-selector-trigger]');

        if (!trigger) {
            return;
        }

        selector.classList.remove('abierto');
        trigger.setAttribute('aria-expanded', 'false');

        trigger.addEventListener('click', function (evento) {
            evento.preventDefault();
            evento.stopPropagation();

            if (selectorAbierto === selector) {
                cerrarSelectorActual();
                return;
            }

            cerrarSelectorActual();

            const nuevoMenu = crearMenuFlotante(selector);
            if (!nuevoMenu) {
                return;
            }

            menuFlotante = nuevoMenu;
            selectorAbierto = selector;
            selector.classList.add('abierto');
            trigger.setAttribute('aria-expanded', 'true');

            posicionarMenu(trigger, menuFlotante);
        });
    });

    document.addEventListener('click', function (evento) {
        if (!selectorAbierto) {
            return;
        }

        if (evento.target.closest('.selector-estado-flyout')) {
            return;
        }

        if (evento.target.closest('[data-selector-estado]')) {
            return;
        }

        cerrarSelectorActual();
    });

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') {
            cerrarSelectorActual();
        }
    });

    window.addEventListener('resize', cerrarSelectorActual);
    window.addEventListener('scroll', cerrarSelectorActual, true);
});
