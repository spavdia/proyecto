document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('tareasApp');
    const botonMenu = document.getElementById('botonMenu');
    const fondoMenu = document.getElementById('fondoMenu');
    const botonNuevaTarea = document.getElementById('botonNuevaTarea');
    const botonCerrarFormulario = document.getElementById('botonCerrarFormulario');
    const panelFormulario = document.getElementById('panelFormularioTarea');
    const layout = document.getElementById('tareasLayout');
    const formulariosEliminar = document.querySelectorAll('.form-eliminar-tarea');

    const selectorActividad = document.getElementById('tipo_actividad');
    const selectorLead = document.getElementById('lead_id');
    const bloqueObjeciones = document.getElementById('bloqueObjecionesFormulario');

    function abrirFormulario() {
        if (!panelFormulario || !layout || !botonNuevaTarea) {
            return;
        }

        panelFormulario.removeAttribute('hidden');
        layout.classList.add('con-formulario');
        botonNuevaTarea.setAttribute('aria-expanded', 'true');
    }

    function cerrarFormulario() {
        if (!panelFormulario || !layout || !botonNuevaTarea) {
            return;
        }

        panelFormulario.setAttribute('hidden', '');
        layout.classList.remove('con-formulario');
        botonNuevaTarea.setAttribute('aria-expanded', 'false');
    }

    function actualizarBloqueObjeciones() {
        if (!selectorActividad || !selectorLead || !bloqueObjeciones) {
            return;
        }

        const opcionLead = selectorLead.options[selectorLead.selectedIndex];
        const estadoLead = opcionLead ? (opcionLead.dataset.estado || '') : '';
        const esObjecion = selectorActividad.value === 'Objeciones' && estadoLead === 'Objeciones';

        if (esObjecion) {
            bloqueObjeciones.removeAttribute('hidden');
            bloqueObjeciones.classList.add('visible');
        } else {
            bloqueObjeciones.setAttribute('hidden', '');
            bloqueObjeciones.classList.remove('visible');
        }
    }

    if (panelFormulario && !panelFormulario.hasAttribute('hidden') && layout) {
        layout.classList.add('con-formulario');
    }

    if (botonNuevaTarea) {
        botonNuevaTarea.addEventListener('click', function () {
            if (panelFormulario && panelFormulario.hasAttribute('hidden')) {
                abrirFormulario();
            } else {
                cerrarFormulario();
            }
        });
    }

    if (botonCerrarFormulario) {
        botonCerrarFormulario.addEventListener('click', function () {
            cerrarFormulario();
        });
    }

    if (selectorActividad) {
        selectorActividad.addEventListener('change', actualizarBloqueObjeciones);
    }

    if (selectorLead) {
        selectorLead.addEventListener('change', actualizarBloqueObjeciones);
    }

    actualizarBloqueObjeciones();

    if (app && botonMenu && fondoMenu) {
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
    }

    formulariosEliminar.forEach(function (formulario) {
        formulario.addEventListener('submit', function (evento) {
            const confirmado = window.confirm('¿Seguro que deseas eliminar esta tarea?');

            if (!confirmado) {
                evento.preventDefault();
            }
        });
    });
});