<?php

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
        $usuario = is_array($usuario) ? $usuario : [];

        $usuarioId = (int) ($usuario['id'] ?? 0);
        $esAdmin = (($usuario['rol'] ?? '') === 'admin');

        $lm = new LeadModel();
        $tm = new TareaModel();

        $leadsPorEstado = $lm->obtenerAgrupadosPorEstado();
        $estadosLista = $lm->getEstados();
        $tareasRetrasadasCount = $tm->getRetrasadasCount($usuarioId, $esAdmin);

        $tm = new TareaModel();

        $nuevasTareas = $tm->getNuevasAsignadasByUsuario((int) $usuario['id']);
        $notificacionTarea = null;

        if (!empty($nuevasTareas)) {
            $tareaNueva = $nuevasTareas[0];

            $imagenesUsuarios = [
                1 => 'user1.png', // admin
                2 => 'user2.png', // Ana
                3 => 'user3.png'  // Juan
            ];

            $usuarioCreadorId = (int) ($tareaNueva['usuario_creador_id'] ?? 0);

            $notificacionTarea = [
                'id'             => (int) ($tareaNueva['id'] ?? 0),
                'lead_id'        => (int) ($tareaNueva['lead_id'] ?? 0),
                'lead_nombre'    => (string) ($tareaNueva['lead_nombre'] ?? ''),
                'tipo_actividad' => (string) ($tareaNueva['tipo_actividad'] ?? ''),
                'fecha_final'    => (string) ($tareaNueva['fecha_final'] ?? ''),
                'created_at'     => (string) ($tareaNueva['created_at'] ?? ''),
                'creador_nombre' => (string) ($tareaNueva['creador_nombre'] ?? 'Usuario'),
                'imagen'         => $imagenesUsuarios[$usuarioCreadorId] ?? 'user1.png'
            ];

            // Marcamos como leídas después de cargarlas para que solo salgan una vez
            $tm->markNuevasComoLeidas((int) $usuario['id']);
        }
        self::view('home/panel_view', [
            'tituloPagina'          => 'PipelineDesk | Panel',
            'usuario'               => $usuario,
            'mensajeFlash'          => $flash['mensaje'] ?? null,
            'iconoFlash'            => $flash['icono'] ?? null,
            'claseFlash'            => $flash['clase'] ?? 'info',
            'leadsPorEstado'        => is_array($leadsPorEstado) ? $leadsPorEstado : [],
            'estadosLista'          => is_array($estadosLista) ? $estadosLista : [],
            'tareasRetrasadasCount' => (int) $tareasRetrasadasCount,
            'notificacionTarea' => $notificacionTarea,
        ]);
    }

    public static function kanban(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $flash = SessionManager::getMensajeFlash();
        $usuario = SessionManager::get('usuario');
        $usuario = is_array($usuario) ? $usuario : [];

        $lm = new LeadModel();
        $leadsPorEstado = $lm->obtenerAgrupadosPorEstado();

        self::view('home/kanban_view', [
            'tituloPagina'   => 'PipelineDesk | Pipeline',
            'usuario'        => $usuario,
            'mensajeFlash'   => $flash['mensaje'] ?? null,
            'iconoFlash'     => $flash['icono'] ?? null,
            'claseFlash'     => $flash['clase'] ?? 'info',
            'leadsPorEstado' => is_array($leadsPorEstado) ? $leadsPorEstado : [],
        ]);
    }

    public static function dashboard(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $flash = SessionManager::getMensajeFlash();
        $usuario = SessionManager::get('usuario');
        $usuario = is_array($usuario) ? $usuario : [];

        $usuarioId = (int) ($usuario['id'] ?? 0);
        $esAdmin = (($usuario['rol'] ?? '') === 'admin');

        $lm = new LeadModel();
        $tm = new TareaModel();

        $serviciosValidos = $lm->getServicios();
        $estadosValidos = $lm->getEstados();
        $origenesValidos = ['formulario_web', 'app_interna'];

        $filtros = [
            'usuario_id'  => $esAdmin ? (int) ($_GET['usuario_id'] ?? 0) : $usuarioId,
            'fecha_desde' => trim($_GET['fecha_desde'] ?? ''),
            'fecha_hasta' => trim($_GET['fecha_hasta'] ?? ''),
            'servicios'   => trim($_GET['servicios'] ?? ''),
            'estado'      => trim($_GET['estado'] ?? ''),
            'origen'      => trim($_GET['origen'] ?? '')
        ];

        if (!$esAdmin) {
            $filtros['usuario_id'] = $usuarioId;
        }

        if ($filtros['servicios'] !== '' && !in_array($filtros['servicios'], $serviciosValidos, true)) {
            $filtros['servicios'] = '';
        }

        if ($filtros['estado'] !== '' && !in_array($filtros['estado'], $estadosValidos, true)) {
            $filtros['estado'] = '';
        }

        if ($filtros['origen'] !== '' && !in_array($filtros['origen'], $origenesValidos, true)) {
            $filtros['origen'] = '';
        }

        $resumenGeneral = $lm->getDashboardResumenGeneral($usuarioId, $esAdmin, $filtros);
        $resumenPipeline = $lm->getResumenPipeline($usuarioId, $esAdmin, $filtros);
        $leadsSinContacto = $lm->getLeadsSinContacto($usuarioId, $esAdmin, $filtros, 6);
        $resumenUsuarios = $esAdmin ? $lm->getResumenPorUsuario($filtros) : [];

        $resumenTareas = $tm->getResumenTareasDashboard($usuarioId, $esAdmin, $filtros);
        $objecionesPorTipo = $tm->getObjecionesPorTipo($usuarioId, $esAdmin, $filtros, 6);
        $solucionesMasUsadas = $tm->getSolucionesMasUsadas($usuarioId, $esAdmin, $filtros, 6);
        $seguimientosUrgentes = $tm->getSeguimientosUrgentes($usuarioId, $esAdmin, $filtros, 6);

        self::view('home/dashboard_view', [
            'tituloPagina'         => 'PipelineDesk | Dashboard',
            'usuario'              => $usuario,
            'mensajeFlash'         => $flash['mensaje'] ?? null,
            'iconoFlash'           => $flash['icono'] ?? null,
            'claseFlash'           => $flash['clase'] ?? 'info',
            'filtros'              => is_array($filtros) ? $filtros : [],
            'usuariosLista'        => is_array($lm->getResponsables()) ? $lm->getResponsables() : [],
            'serviciosLista'       => is_array($serviciosValidos) ? $serviciosValidos : [],
            'estadosLista'         => is_array($estadosValidos) ? $estadosValidos : [],
            'resumenGeneral'       => is_array($resumenGeneral) ? $resumenGeneral : [],
            'resumenPipeline'      => is_array($resumenPipeline) ? $resumenPipeline : [],
            'resumenTareas'        => is_array($resumenTareas) ? $resumenTareas : [],
            'objecionesPorTipo'    => is_array($objecionesPorTipo) ? $objecionesPorTipo : [],
            'solucionesMasUsadas'  => is_array($solucionesMasUsadas) ? $solucionesMasUsadas : [],
            'seguimientosUrgentes' => is_array($seguimientosUrgentes) ? $seguimientosUrgentes : [],
            'leadsSinContacto'     => is_array($leadsSinContacto) ? $leadsSinContacto : [],
            'resumenUsuarios'      => is_array($resumenUsuarios) ? $resumenUsuarios : []
        ]);
    }
}
