"use strict";

//FUNCTION
document.addEventListener('DOMContentLoaded', function () {
    const seccionVisor = document.querySelector('#visorPresentacion');

    if (!seccionVisor) {
        return;
    }
    //recupero diapositivas con dataset y parseo
    const datosJson = seccionVisor.dataset.diapositivas || '[]';
    const diapositivas = JSON.parse(datosJson);

    if (!diapositivas.length) {
        return;
    }

    const imagen = seccionVisor.querySelector('#imagenDiapositiva');
    const titulo = seccionVisor.querySelector('#tituloDiapositiva');
    const texto = seccionVisor.querySelector('#textoDiapositiva');
    const contador = seccionVisor.querySelector('#contadorDiapositiva');
    const total = seccionVisor.querySelector('#totalDiapositivas');
    const botonAnterior = seccionVisor.querySelector('#botonAnterior');
    const botonSiguiente = seccionVisor.querySelector('#botonSiguiente');
    const diapositivaActiva = seccionVisor.querySelector('#diapositivaActiva');

    let indiceActual = 0;
    total.textContent = diapositivas.length;

    function renderizarDiapositiva(indice) {
        const item = diapositivas[indice];

        imagen.src = item.imagen;
        imagen.alt = item.titulo;
        titulo.textContent = item.titulo;
        texto.textContent = item.texto;
        contador.textContent = indice + 1;
    }

    function siguienteDiapositiva() {
        indiceActual = (indiceActual + 1) % diapositivas.length;
        renderizarDiapositiva(indiceActual);
    }

    function anteriorDiapositiva() {
        indiceActual = (indiceActual - 1 + diapositivas.length) % diapositivas.length;
        renderizarDiapositiva(indiceActual);
    }

    botonSiguiente.addEventListener('click', siguienteDiapositiva);
    botonAnterior.addEventListener('click', anteriorDiapositiva);
    diapositivaActiva.addEventListener('click', siguienteDiapositiva);

    diapositivaActiva.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            siguienteDiapositiva();
        }

        if (e.key === 'ArrowRight') {
            siguienteDiapositiva();
        }

        if (e.key === 'ArrowLeft') {
            anteriorDiapositiva();
        }
    });

    renderizarDiapositiva(indiceActual);
});