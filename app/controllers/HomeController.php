<?php

namespace Sergio\App\controllers;

class HomeController extends Controller
{

    public static function index()
    {

        self::view("home/index", [
            'titulo'=> 'CRM Pipeline',
            'mensaje'=> 'Fase 0 funcionando'
        ]);
    }
}
