document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('kanbanApp');
    const tablero = document.getElementById('kanbanTablero');
    const botonMenu = document.getElementById('botonMenu');
    const fondoMenu = document.getElementById('fondoMenu');
    const mensaje = document.getElementById('mensajeKanban');
    const botonConfig = document.getElementById('botonConfigKanban');
    const panelConfig = document.getElementById('panelConfigKanban');

    if (!app || !tablero) {
        return;
    }

    const baseUrl = app.dataset.baseUrl || '/';
    const claveConfig = 'pipelineDeskCamposKanban';

    let tarjetaActiva = null;
    let columnaOrigen = null;
    let estadoOrigen = '';

    const camposObligatorios = ['lead_nombre', 'servicios'];

    const camposPorDefecto = {
        lead_nombre: true,
        email: false,
        telefono: true,
        responsable_nombre: true,
        servicios: true,
        valor: true,
        prioridad: true,
        estado: false,
        indicaciones: false,
        ultimo_contacto: false,
        origen: false,
        lead_score: false
    };

    function formatearEuros(valor) {
        return Number(valor).toLocaleString('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' €';
    }

    function mostrarMensaje(texto, tipo) {
        if (!mensaje) {
            return;
        }

        mensaje.textContent = texto;
        mensaje.className = 'mensaje-kanban visible ' + (tipo || '');

        window.clearTimeout(mostrarMensaje.temporizador);
        mostrarMensaje.temporizador = window.setTimeout(function () {
            mensaje.className = 'mensaje-kanban';
        }, 2400);
    }

    function actualizarResumenColumnas() {
        const columnas = tablero.querySelectorAll('.kanban-columna');

        columnas.forEach(function (columna) {
            const contador = columna.querySelector('[data-contador]');
            const totalValor = columna.querySelector('[data-total-valor]');
            const tarjetas = columna.querySelectorAll('.kanban-tarjeta');
            let suma = 0;

            tarjetas.forEach(function (tarjeta) {
                suma += parseFloat(tarjeta.dataset.valor || '0');
            });

            if (contador) {
                contador.textContent = String(tarjetas.length);
            }

            if (totalValor) {
                totalValor.textContent = formatearEuros(suma);
            }

            const lista = columna.querySelector('.kanban-lista');
            if (!lista) {
                return;
            }

            let vacio = lista.querySelector('.kanban-vacio');

            if (tarjetas.length === 0) {
                if (!vacio) {
                    vacio = document.createElement('div');
                    vacio.className = 'kanban-vacio';
                    vacio.textContent = 'Sin leads';
                    lista.appendChild(vacio);
                }
            } else if (vacio) {
                vacio.remove();
            }
        });
    }

    function limpiarDestinos() {
        const columnas = tablero.querySelectorAll('.kanban-columna');
        columnas.forEach(function (columna) {
            columna.classList.remove('destino');
        });
    }

    function leerConfigGuardada() {
        try {
            const guardada = window.localStorage.getItem(claveConfig);
            if (!guardada) {
                return { ...camposPorDefecto };
            }

            const config = JSON.parse(guardada);
            return { ...camposPorDefecto, ...config };
        } catch (error) {
            return { ...camposPorDefecto };
        }
    }

    function guardarConfig(config) {
        window.localStorage.setItem(claveConfig, JSON.stringify(config));
    }

    function aplicarConfigTarjetas(config) {
        const campos = tablero.querySelectorAll('[data-campo]');

        campos.forEach(function (elemento) {
            const campo = elemento.dataset.campo || '';
            const visible = config[campo] !== false;

            if (visible || camposObligatorios.includes(campo)) {
                elemento.classList.remove('oculto-campo');
            } else {
                elemento.classList.add('oculto-campo');
            }
        });
    }

    function sincronizarChecks(config) {
        const checks = document.querySelectorAll('[data-campo-config]');

        checks.forEach(function (check) {
            const campo = check.dataset.campoConfig || '';
            check.checked = config[campo] !== false || camposObligatorios.includes(campo);

            if (camposObligatorios.includes(campo)) {
                check.checked = true;
                check.disabled = true;
            }
        });
    }

    function obtenerConfigActual() {
        const config = { ...camposPorDefecto };
        const checks = document.querySelectorAll('[data-campo-config]');

        checks.forEach(function (check) {
            const campo = check.dataset.campoConfig || '';

            if (camposObligatorios.includes(campo)) {
                config[campo] = true;
            } else {
                config[campo] = check.checked;
            }
        });

        return config;
    }

    function abrirCerrarConfig(forzar) {
        if (!panelConfig || !botonConfig) {
            return;
        }

        const abiertoActual = !panelConfig.hasAttribute('hidden');
        const abiertoNuevo = typeof forzar === 'boolean' ? forzar : !abiertoActual;

        if (abiertoNuevo) {
            panelConfig.removeAttribute('hidden');
        } else {
            panelConfig.setAttribute('hidden', '');
        }

        botonConfig.setAttribute('aria-expanded', abiertoNuevo ? 'true' : 'false');
    }

    const tarjetas = tablero.querySelectorAll('.kanban-tarjeta');
    tarjetas.forEach(function (tarjeta) {
        tarjeta.addEventListener('dragstart', function (evento) {
            tarjetaActiva = tarjeta;
            columnaOrigen = tarjeta.closest('.kanban-columna');
            estadoOrigen = tarjeta.dataset.estado || '';

            tarjeta.classList.add('arrastrando');

            if (evento.dataTransfer) {
                evento.dataTransfer.effectAllowed = 'move';
                evento.dataTransfer.setData('text/plain', tarjeta.dataset.id || '');
            }
        });

        tarjeta.addEventListener('dragend', function () {
            tarjeta.classList.remove('arrastrando');
            limpiarDestinos();
            tarjetaActiva = null;
        });
    });

    const columnas = tablero.querySelectorAll('.kanban-columna');
    columnas.forEach(function (columna) {
        columna.addEventListener('dragover', function (evento) {
            evento.preventDefault();
            columna.classList.add('destino');
        });

        columna.addEventListener('dragleave', function () {
            columna.classList.remove('destino');
        });

        columna.addEventListener('drop', function (evento) {
            evento.preventDefault();
            columna.classList.remove('destino');

            if (!tarjetaActiva) {
                return;
            }

            const estadoDestino = columna.dataset.estado || '';
            const listaDestino = columna.querySelector('.kanban-lista');

            if (!listaDestino) {
                return;
            }

            if (estadoDestino === '' || estadoDestino === estadoOrigen) {
                return;
            }

            const leadId = tarjetaActiva.dataset.id || '';
            if (leadId === '') {
                return;
            }

            const listaOrigen = columnaOrigen ? columnaOrigen.querySelector('.kanban-lista') : null;
            const siguienteHermano = tarjetaActiva.nextElementSibling;

            listaDestino.appendChild(tarjetaActiva);
            tarjetaActiva.dataset.estado = estadoDestino;
            actualizarResumenColumnas();

            const datos = new FormData();
            datos.append('lead_id', leadId);
            datos.append('estado', estadoDestino);

            fetch(baseUrl + 'pipeline/cambiar-estado', {
                method: 'POST',
                body: datos,
                credentials: 'same-origin'
            })
                .then(function (respuesta) {
                    return respuesta.json();
                })
                .then(function (json) {
                    if (!json.ok) {
                        throw new Error(json.mensaje || 'No se ha podido actualizar el estado.');
                    }

                    mostrarMensaje(json.mensaje || 'Estado actualizado.', 'exito');
                    estadoOrigen = estadoDestino;
                })
                .catch(function (error) {
                    if (listaOrigen) {
                        if (siguienteHermano) {
                            listaOrigen.insertBefore(tarjetaActiva, siguienteHermano);
                        } else {
                            listaOrigen.appendChild(tarjetaActiva);
                        }
                        tarjetaActiva.dataset.estado = estadoOrigen;
                        actualizarResumenColumnas();
                    }

                    mostrarMensaje(error.message || 'Ha ocurrido un error.', 'error');
                });
        });
    });

    if (botonMenu && fondoMenu) {
        botonMenu.addEventListener('click', function () {
            const abierto = app.classList.toggle('menu-abierto');
            botonMenu.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        });

        fondoMenu.addEventListener('click', function () {
            app.classList.remove('menu-abierto');
            botonMenu.setAttribute('aria-expanded', 'false');
        });
    }

    if (botonConfig && panelConfig) {
        botonConfig.addEventListener('click', function (evento) {
            evento.stopPropagation();
            abrirCerrarConfig();
        });

        panelConfig.addEventListener('click', function (evento) {
            evento.stopPropagation();
        });

        document.addEventListener('click', function () {
            abrirCerrarConfig(false);
        });

        const checks = document.querySelectorAll('[data-campo-config]');
        checks.forEach(function (check) {
            check.addEventListener('change', function () {
                const config = obtenerConfigActual();
                guardarConfig(config);
                aplicarConfigTarjetas(config);
            });
        });
    }

    const configInicial = leerConfigGuardada();
    sincronizarChecks(configInicial);
    aplicarConfigTarjetas(configInicial);
    actualizarResumenColumnas();
});