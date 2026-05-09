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
        $diapositivas = self::getDiapositivasPresentacion();

        self::view('home/index_view', [
            'tituloPagina' => 'PipelineDesk | Inicio',
            'mensajeFlash' => $flash['mensaje'] ?? null,
            'iconoFlash'   => $flash['icono'] ?? null,
            'claseFlash'   => $flash['clase'] ?? 'info',
            'diapositivas' => $diapositivas
        ]);
    }

    private static function getDiapositivasPresentacion(): array
    {
        return [
            [
                'imagen' => BASE_URL . 'img/presentacion/page_1.png',
                'titulo' => 'PipelineDesk CRM',
                'texto'  => 'Presentacion del proyecto CRM Pipeline para centros de formacion, desarrollado con PHP, MySQL, JavaScript, HTML, CSS y Tailwind.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_2.png',
                'titulo' => 'Puntos a tratar',
                'texto'  => 'Recorrido general por el objetivo, funcionalidades, arquitectura, fases, dificultades y mejoras futuras del proyecto.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_3.png',
                'titulo' => 'Objetivo del proyecto',
                'texto'  => 'Crear un CRM ligero para captar interesados, hacer seguimiento, mover leads por el embudo y analizar resultados.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_4.png',
                'titulo' => 'Contexto y mercado objetivo',
                'texto'  => 'Solucion vertical para academias pequenas y medianas que necesitan control comercial sin la complejidad de un CRM enterprise.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_5.png',
                'titulo' => 'Funcionalidades implementadas',
                'texto'  => 'Resumen de login, gestion de leads, panel, Kanban, detalle comercial, tareas y dashboard con KPIs.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_6.png',
                'titulo' => 'Embudo comercial',
                'texto'  => 'El lead avanza por estados desde nuevo hasta ganado o perdido, manteniendo historico y visibilidad del progreso.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_7.png',
                'titulo' => 'Arquitectura final del sistema',
                'texto'  => 'Aplicacion PHP MVC con front controller, router, controladores, modelos, vistas y persistencia en MySQL.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_8.png',
                'titulo' => 'Estructura de carpetas',
                'texto'  => 'Organizacion real del proyecto crm-pipeline, separando app, database, lib, public, routes y documentacion.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_9.png',
                'titulo' => 'Modelo de datos',
                'texto'  => 'El lead es el centro del sistema y se relaciona con usuarios, notas, historial, tareas, objetivos y notificaciones.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_10.png',
                'titulo' => 'Consultas y acceso a datos',
                'texto'  => 'La analitica se resuelve desde modelos con SQL preparado, joins, filtros, agregaciones y control de permisos.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_11.png',
                'titulo' => 'Interfaz de usuario',
                'texto'  => 'Vistas principales del CRM: login, dashboard, listado, Kanban, nuevo lead y detalle comercial.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_12.png',
                'titulo' => 'Mapa de navegacion',
                'texto'  => 'Separacion entre zona publica y zona privada, con rutas para panel, pipeline, dashboard, leads, tareas y cierre de sesion.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_13.png',
                'titulo' => 'Desarrollo por fases',
                'texto'  => 'Construccion incremental desde la base MVC hasta login, leads, Kanban, tareas, dashboard, Tailwind y mejoras de UX.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_14.png',
                'titulo' => 'Aspectos importantes',
                'texto'  => 'Decisiones tecnicas clave: MVC claro, POST/Redirect/GET, SQL preparado, historico comercial, Kanban asincrono y toasts.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_15.png',
                'titulo' => 'Dificultad 1: Tailwind',
                'texto'  => 'Integracion de Tailwind sin romper CSS previo, layout comun, modo oscuro ni componentes ya existentes.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_16.png',
                'titulo' => 'Dificultad 2: consultas complejas',
                'texto'  => 'Dashboard, tareas y objeciones combinan filtros, agregaciones, relaciones y automatizaciones entre entidades.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_17.png',
                'titulo' => 'Dificultad 3: sincronizacion',
                'texto'  => 'Los cambios de estado deben actualizar lead, notas, historial, tareas y dashboard de forma coherente.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_18.png',
                'titulo' => 'Cambio de plan inicial',
                'texto'  => 'Evolucion desde una arquitectura hexagonal con Java hacia una aplicacion MVC en PHP, MySQL, JavaScript, CSS y Tailwind.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_19.png',
                'titulo' => 'Mejora 1: objeciones',
                'texto'  => 'Propuesta para dar mas peso a las objeciones, registrando causa, impacto, respuesta y estado de resolucion.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_20.png',
                'titulo' => 'Mejora 2: usuarios',
                'texto'  => 'Administracion de usuarios desde la aplicacion para crear cuentas, asignar roles, activar usuarios y resetear contrasenas.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_21.png',
                'titulo' => 'Mejora 3: configuracion adaptable',
                'texto'  => 'Configuracion de intereses, servicios, etapas y reglas para adaptar el CRM a distintos negocios.'
            ],
            [
                'imagen' => BASE_URL . 'img/presentacion/page_22.png',
                'titulo' => 'Cierre de la presentacion',
                'texto'  => 'PipelineDesk demuestra una aplicacion web academica coherente, mantenible y orientada a procesos comerciales reales.'
            ]
        ];
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

        $imagenesUsuarios = [
            1 => 'user1.png',
            2 => 'user2.png',
            3 => 'user3.png'
        ];

        $nuevasTareas = $tm->getNuevasAsignadasByUsuario($usuarioId);
        $notificacionTarea = null;

        if (!empty($nuevasTareas)) {
            $tareaNueva = $nuevasTareas[0];
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

            $tm->markNuevasComoLeidas($usuarioId);
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
            'notificacionTarea'     => $notificacionTarea
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
        $usuarioObjetivoId = (!$esAdmin) ? $usuarioId : (int) ($filtros['usuario_id'] ?? 0);
        $objetivoMes = $lm->getObjetivoMesActual($usuarioObjetivoId, $esAdmin && $usuarioObjetivoId === 0);
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
            'objetivoMes'          => is_array($objetivoMes) ? $objetivoMes : [],
            'resumenPipeline'      => is_array($resumenPipeline) ? $resumenPipeline : [],
            'resumenTareas'        => is_array($resumenTareas) ? $resumenTareas : [],
            'objecionesPorTipo'    => is_array($objecionesPorTipo) ? $objecionesPorTipo : [],
            'solucionesMasUsadas'  => is_array($solucionesMasUsadas) ? $solucionesMasUsadas : [],
            'seguimientosUrgentes' => is_array($seguimientosUrgentes) ? $seguimientosUrgentes : [],
            'leadsSinContacto'     => is_array($leadsSinContacto) ? $leadsSinContacto : [],
            'resumenUsuarios'      => is_array($resumenUsuarios) ? $resumenUsuarios : []
        ]);
    }

    public static function guardarObjetivoMes(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $usuario = is_array($usuario) ? $usuario : [];

        if (($usuario['rol'] ?? '') !== 'admin') {
            SessionManager::setMensajeFlash('No tienes permisos para actualizar el objetivo mensual.', '⚠', 'error');
            header('Location: ' . BASE_URL . 'dashboard');
            exit();
        }

        $objetivo = max(0, (int) ($_POST['objetivo_leads'] ?? 0));
        $lm = new LeadModel();
        $guardado = $lm->saveObjetivoMesActual($objetivo, (int) ($usuario['id'] ?? 0));

        SessionManager::setMensajeFlash(
            $guardado ? 'Objetivo mensual actualizado correctamente.' : 'No se ha podido actualizar el objetivo mensual.',
            $guardado ? '✅' : '⚠',
            $guardado ? 'exito' : 'error'
        );

        header('Location: ' . BASE_URL . 'dashboard');
        exit();
    }

    public static function politicaPrivacidad(): void
    {
        self::view('home/privacy_view', [
            'tituloPagina' => 'PipelineDesk | Politica de privacidad'
        ]);
    }
}
