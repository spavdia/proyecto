<?php

declare(strict_types=1);

namespace Sergio\App\Controllers;

use Sergio\Lib\SessionManager;
use Sergio\App\models\LeadModel;

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
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $flash = SessionManager::getMensajeFlash();
        $usuario = SessionManager::get('usuario');

        $lm = new LeadModel();
        $leadsPorEstado = $lm->obtenerAgrupadosPorEstado();
        $estadosLista = $lm->getEstados();

        self::view('home/panel_view', [
            'tituloPagina'   => 'PipelineDesk | Panel',
            'usuario'        => $usuario,
            'mensajeFlash'   => $flash['mensaje'] ?? null,
            'iconoFlash'     => $flash['icono'] ?? null,
            'claseFlash'     => $flash['clase'] ?? 'info',
            'leadsPorEstado' => $leadsPorEstado,
            'estadosLista'   => $estadosLista
        ]);
    }

    public static function kanban(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $flash = SessionManager::getMensajeFlash();
        $usuario = SessionManager::get('usuario');

        $lm = new LeadModel();
        $leadsPorEstado = $lm->obtenerAgrupadosPorEstado();

        self::view('home/kanban_view', [
            'tituloPagina'   => 'PipelineDesk | Pipeline',
            'usuario'        => $usuario,
            'mensajeFlash'   => $flash['mensaje'] ?? null,
            'iconoFlash'     => $flash['icono'] ?? null,
            'claseFlash'     => $flash['clase'] ?? 'info',
            'leadsPorEstado' => $leadsPorEstado,
            'estadosLista'   => $lm->getEstados()
        ]);
    }
}