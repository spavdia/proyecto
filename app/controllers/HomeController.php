<?php

declare(strict_types=1);

namespace Sergio\App\controllers;

use Sergio\Lib\SessionManager;

class HomeController extends Controller
{
    public static function index(): void
    {
        SessionManager::iniciarSesion();
        $flash = SessionManager::getMensajeFlash();

        self::view('home/index_view', [
            'titulo' => 'CRM Pipeline',
            'mensaje' => 'Fase 0 funcionando',
            'flash' => $flash,
        ]);
    }

    public static function panel(): void
    {
        SessionManager::usuarioNoAutenticado('usuario', '/login');

        $usuario = SessionManager::get('usuario');
        $flash = SessionManager::getMensajeFlash();

        self::view('home/panel_view', [
            'titulo' => 'Panel privado',
            'usuario' => $usuario,
            'flash' => $flash,
        ]);
    }
}
