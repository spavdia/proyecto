<?php

namespace Sergio\App\controllers;

class Controller
{

    // Método común para cargar vistas
    protected static function view(string $vista, array $datos = [])
    {

        // Convertimos el array en variables
        extract($datos);

        // Cargamos la vista
        require_once "../app/views/$vista.php";
    }
}
