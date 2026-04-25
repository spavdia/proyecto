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
            'tituloPagina' => 'PipelineDesk | Inicio',
            'mensajeFlash' => $flash['mensaje'] ?? null,
            'iconoFlash'   => $flash['icono'] ?? null,
            'claseFlash'   => $flash['clase'] ?? 'info'
        ]);
    }

    public static function panel(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', '/login');

        $usuario = SessionManager::get('usuario');
        $flash = SessionManager::getMensajeFlash();

        self::view('home/panel_view', [
            'tituloPagina' => 'PipelineDesk | Panel',
            'usuario'      => $usuario,
            'mensajeFlash' => $flash['mensaje'] ?? null,
            'iconoFlash'   => $flash['icono'] ?? null,
            'claseFlash'   => $flash['clase'] ?? 'info'
        ]);
    }
}
