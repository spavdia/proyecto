<?php

declare(strict_types=1);

namespace Sergio\App\Controllers;

use Sergio\Lib\SessionManager;
use Sergio\App\models\LeadModel;
use Sergio\App\models\TareaModel;

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
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $esAdmin = (($usuario['rol'] ?? '') === 'admin');

        $lm = new LeadModel();
        $leadsPorEstado = $lm->obtenerAgrupadosPorEstado();
        $estadosLista = $lm->getEstados();

        $tm = new TareaModel();
        $tareasRetrasadasCount = $tm->getRetrasadasCount($usuarioId, $esAdmin);

        self::view('home/panel_view', [
            'tituloPagina'          => 'PipelineDesk | Panel',
            'usuario'               => $usuario,
            'mensajeFlash'          => $flash['mensaje'] ?? null,
            'iconoFlash'            => $flash['icono'] ?? null,
            'claseFlash'            => $flash['clase'] ?? 'info',
            'leadsPorEstado'        => $leadsPorEstado,
            'estadosLista'          => $estadosLista,
            'tareasRetrasadasCount' => $tareasRetrasadasCount
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