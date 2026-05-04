document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('tareasApp');
    const botonMenu = document.getElementById('botonMenu');
    const fondoMenu = document.getElementById('fondoMenu');
    const botonNuevaTarea = document.getElementById('botonNuevaTarea');
    const botonCerrarFormulario = document.getElementById('botonCerrarFormulario');
    const panelFormulario = document.getElementById('panelFormularioTarea');
    const tareasLayout = document.getElementById('tareasLayout');
    const selectTipoActividad = document.getElementById('tipo_actividad');
    const selectLead = document.getElementById('lead_id');
    const bloqueObjeciones = document.getElementById('bloqueObjecionesFormulario');

    function abrirMenu() {
        if (!app || !botonMenu) {
            return;
        }
        app.classList.add('menu-abierto');
        botonMenu.setAttribute('aria-expanded', 'true');
    }

    function cerrarMenu() {
        if (!app || !botonMenu) {
            return;
        }
        app.classList.remove('menu-abierto');
        botonMenu.setAttribute('aria-expanded', 'false');
    }

    if (botonMenu) {
        botonMenu.addEventListener('click', function () {
            if (!app) {
                return;
            }

            const abierto = app.classList.toggle('menu-abierto');
            botonMenu.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        });
    }

    if (fondoMenu) {
        fondoMenu.addEventListener('click', cerrarMenu);
    }

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') {
            cerrarMenu();
        }
    });

    function abrirFormulario() {
        if (!panelFormulario || !tareasLayout || !botonNuevaTarea) {
            return;
        }

        panelFormulario.hidden = false;
        tareasLayout.classList.add('con-formulario');
        botonNuevaTarea.setAttribute('aria-expanded', 'true');
    }

    function cerrarFormulario() {
        if (!panelFormulario || !tareasLayout || !botonNuevaTarea) {
            return;
        }

        panelFormulario.hidden = true;
        tareasLayout.classList.remove('con-formulario');
        botonNuevaTarea.setAttribute('aria-expanded', 'false');
    }

    if (botonNuevaTarea) {
        botonNuevaTarea.addEventListener('click', function () {
            if (!panelFormulario || panelFormulario.hidden) {
                abrirFormulario();
                return;
            }

            cerrarFormulario();
        });
    }

    if (botonCerrarFormulario) {
        botonCerrarFormulario.addEventListener('click', cerrarFormulario);
    }

    function actualizarBloqueObjeciones() {
        if (!selectTipoActividad || !selectLead || !bloqueObjeciones) {
            return;
        }

        const tipoActividad = selectTipoActividad.value;
        const opcionLead = selectLead.options[selectLead.selectedIndex];
        const estadoLead = opcionLead ? (opcionLead.dataset.estado || '') : '';
        const mostrar = tipoActividad === 'Objeciones' && estadoLead === 'Objeciones';

        bloqueObjeciones.hidden = !mostrar;
        bloqueObjeciones.classList.toggle('visible', mostrar);
    }

    if (selectTipoActividad) {
        selectTipoActividad.addEventListener('change', actualizarBloqueObjeciones);
    }

    if (selectLead) {
        selectLead.addEventListener('change', actualizarBloqueObjeciones);
    }

    actualizarBloqueObjeciones();

    const selectoresEstadoTabla = document.querySelectorAll('.selector-estado-tabla');

    function aplicarClaseEstadoSelect(select) {
        select.classList.remove('estado-pendiente', 'estado-curso', 'estado-terminada');

        if (select.value === 'Pendiente') {
            select.classList.add('estado-pendiente');
        } else if (select.value === 'En curso') {
            select.classList.add('estado-curso');
        } else if (select.value === 'Terminada') {
            select.classList.add('estado-terminada');
        }
    }

    selectoresEstadoTabla.forEach(function (select) {
        aplicarClaseEstadoSelect(select);

        select.addEventListener('change', function () {
            aplicarClaseEstadoSelect(select);

            const formulario = select.closest('form');
            if (!formulario) {
                return;
            }

            select.classList.add('guardando');
            formulario.submit();
        });
    });
});