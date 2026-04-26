<?php
/*
index() → listado
crear() → mostrar formulario
guardar() → procesar alta
editar() → mostrar edición
actualizar() → guardar cambios
ver() → detalle
 */

declare(strict_types=1);

namespace Sergio\App\Controllers;

use Sergio\Lib\SessionManager;
use Sergio\App\models\leadModel;

class LeadController extends Controller
{
    //mostrar FORM Contacto
    public static function mostrarContacto(): void
    {
        SessionManager::iniciarSesion();

        $flash = SessionManager::getMensajeFlash();
        $lm = new LeadModel();
        $serviciosLista = $lm->getServicios();

        self::view('home/contacto_view', [
            'tituloPagina' => 'PipelineDesk | Contacto',
            'errores'      => [],
            'lead_nombre'  => '',
            'email'        => '',
            'telefono'     => '',
            'servicios'    => '',
            'indicaciones' => '',
            'mensajeFlash' => $flash['mensaje'] ?? null,
            'iconoFlash'   => $flash['icono'] ?? null,
            'claseFlash'   => $flash['clase'] ?? 'info',
            'serviciosLista' => $serviciosLista
        ]);
    }

    //POST -> formulario contacto
    public static function guardarContacto(): void
    {
        SessionManager::iniciarSesion();
        $errores = [];
        $leadNombre = trim($_POST['lead_nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $servicios = trim($_POST['servicios'] ?? '');
        $indicaciones = trim($_POST['indicaciones'] ?? '');

        //validaciones campos obligatorios o de formatos
        if ($leadNombre === "") {
            $errores['lead_nombre'] = "Error. El nombre es obligatorio";
        }
        if ($email === "") {
            $errores['email'] = "Error. El email es obligatorio";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = "Error. Email con formato inválido";
        }
        if ($telefono === "") {
            $errores['telefono'] = "Error. El teléfono es obligatorio";
        }
        if ($servicios === '') {
            $errores['servicios'] = 'Error. Debes seleccionar un servicio.';
        }
        if (!empty($errores)) {
            if (!empty($errores)) {
                $leadModel = new LeadModel();

                self::view('home/contacto_view', [
                    'tituloPagina' => 'PipelineDesk | Contacto',
                    'errores'      => $errores,
                    'lead_nombre'  => $leadNombre,
                    'email'        => $email,
                    'telefono'     => $telefono,
                    'servicios'    => $servicios,
                    'indicaciones' => $indicaciones,
                    'mensajeFlash' => null,
                    'iconoFlash'   => null,
                    'claseFlash'   => 'error',
                    'serviciosLista' => $leadModel->getServicios()
                ]);
                return;
            }
        }


        $lm = new LeadModel();

        $guardado = $lm->create([
            'lead_nombre'     => $leadNombre,
            'servicios'       => $servicios,
            'indicaciones'    => $indicaciones !== '' ? $indicaciones : null,
            'lead_score'      => 0,
            'email'           => $email !== '' ? $email : null,
            'telefono'        => $telefono !== '' ? $telefono : null,
            'origen'          => 'formulario_web'
        ]);

        if (!$guardado) {
            self::view('home/contacto_view', [
                'tituloPagina' => 'PipelineDesk | Contacto',
                'errores'      => [],
                'lead_nombre'  => $leadNombre,
                'email'        => $email,
                'telefono'     => $telefono,
                'servicios'    => $servicios,
                'indicaciones' => $indicaciones,
                'mensajeFlash' => 'No se ha podido enviar el formulario. Inténtalo de nuevo.',
                'iconoFlash'   => '⚠',
                'claseFlash'   => 'error',
                'serviciosLista' => $lm->getServicios()
            ]);
            return;
        }

        SessionManager::setMensajeFlash(
            'Gracias, hemos recibido tu solicitud correctamente. Te contactaremos en breve',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'contacto');
        exit();
    }

    // crear desde App interna
    public static function crearLead(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $flash = SessionManager::getMensajeFlash();
        $datosAntiguos = SessionManager::get('datos_lead') ?? [];
        SessionManager::eliminar('datos_lead');

        $lm = new LeadModel();

        self::view('lead/lead_create_view', [
            'tituloPagina'   => 'PipelineDesk | Nuevo lead',
            'mensajeFlash'   => $flash['mensaje'] ?? null,
            'iconoFlash'     => $flash['icono'] ?? null,
            'claseFlash'     => $flash['clase'] ?? 'info',
            'datosAntiguos'  => $datosAntiguos,
            'serviciosLista' => $lm->getServicios(),
            'prioridades'    => $lm->getPrioridades()
        ]);
    }
    //POST
    public static function guardarLead(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');

        $leadNombre = trim($_POST['lead_nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $servicios = trim($_POST['servicios'] ?? '');
        $indicaciones = trim($_POST['indicaciones'] ?? '');
        $prioridad = trim($_POST['prioridad'] ?? 'Media');
        $valor = trim($_POST['valor'] ?? '');

        if ($leadNombre === '' || $servicios === '') {
            SessionManager::setMensajeFlash(
                'Debes rellenar al menos el nombre del lead y el servicio.',
                '⚠',
                'error'
            );

            SessionManager::set('datos_lead', [
                'lead_nombre'  => $leadNombre,
                'email'        => $email,
                'telefono'     => $telefono,
                'servicios'    => $servicios,
                'indicaciones' => $indicaciones,
                'prioridad'    => $prioridad,
                'valor'        => $valor
            ]);

            header('Location: ' . BASE_URL . 'leads/nuevo');
            exit();
        }

        $lm = new LeadModel();

        $guardado = $lm->create([
            'lead_nombre'     => $leadNombre,
            'responsable_id'  => $usuario['id'] ?? null,
            'servicios'       => $servicios,
            'indicaciones'    => $indicaciones !== '' ? $indicaciones : null,
            'lead_score'      => 0,
            'email'           => $email !== '' ? $email : null,
            'telefono'        => $telefono !== '' ? $telefono : null,
            'valor'           => $valor !== '' ? (float) $valor : null,
            'ultimo_contacto' => null,
            'prioridad'       => $prioridad !== '' ? $prioridad : 'Media',
            'origen'          => 'app_interna'
        ]);

        if (!$guardado) {
            SessionManager::setMensajeFlash(
                'No se ha podido guardar el lead.',
                '⚠',
                'error'
            );

            header('Location: ' . BASE_URL . 'leads/nuevo');
            exit();
        }

        SessionManager::setMensajeFlash(
            'Lead creado correctamente.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'panel');
        exit();
    }
}
