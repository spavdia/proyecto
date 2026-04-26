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
    public static function mostrarFormContacto(): void
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
    public static function nuevoContacto(): void
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
    public static function mostrarFormLead(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $flash = SessionManager::getMensajeFlash();
        $lm = new LeadModel();

        self::view('lead/lead_create_view', [
            'tituloPagina'   => 'PipelineDesk | Nuevo lead',
            'errores'        => [],
            'lead_nombre'    => '',
            'email'          => '',
            'telefono'       => '',
            'servicios'      => '',
            'indicaciones'   => '',
            'prioridad'      => PRIORIDAD_POR_DEFECTO,
            'valor'          => '',
            'estado'         => 'Nuevo Lead',
            'responsable_id' => USUARIO_POR_DEFECTO,
            'mensajeFlash'   => $flash['mensaje'] ?? null,
            'iconoFlash'     => $flash['icono'] ?? null,
            'claseFlash'     => $flash['clase'] ?? 'info',
            'serviciosLista' => $lm->getServicios(),
            'prioridades'    => $lm->getPrioridades(),
            'estadosLista'   => $lm->getEstados(),
            'responsables'   => $lm->getResponsables()
        ]);
    }
    //POST formulario manual con asignacion de responsable y estado
    public static function nuevoLead(): void

    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $lm = new LeadModel();
        $errores = [];

        $leadNombre   = trim($_POST['lead_nombre'] ?? '');
        $email        = trim($_POST['email'] ?? '');
        $telefono     = trim($_POST['telefono'] ?? '');
        $servicios    = trim($_POST['servicios'] ?? '');
        $indicaciones = trim($_POST['indicaciones'] ?? '');
        $prioridad    = trim($_POST['prioridad'] ?? PRIORIDAD_POR_DEFECTO);
        $valor        = trim($_POST['valor'] ?? '');
        $estado       = trim($_POST['estado'] ?? 'Nuevo Lead');
        $responsableId = (int)($_POST['responsable_id'] ?? USUARIO_POR_DEFECTO);

        if ($leadNombre === '') {
            $errores['lead_nombre'] = 'Error. Debes rellenar el nombre del lead.';
        } elseif (mb_strlen($leadNombre) < 2) {
            $errores['lead_nombre'] = 'Error. El nombre del lead es demasiado corto.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Error. El email no tiene un formato válido.';
        }

        if ($telefono === '') {
            $errores['telefono'] = 'Error. El teléfono no tiene un formato válido.';
        }

        if ($servicios === '') {
            $errores['servicios'] = 'Error. Debes seleccionar un servicio.';
        }

        if (!in_array($prioridad, $lm->getPrioridades(), true)) {
            $errores['prioridad'] = 'Error. Debes seleccionar una prioridad válida.';
        }

        if (!in_array($estado, $lm->getEstados(), true)) {
            $errores['estado'] = 'Error. Debes seleccionar un estado válido.';
        }

        $responsables = $lm->getResponsables();
        $idsResponsables = array_map(static fn($item) => (int)$item['id'], $responsables);

        if (!in_array($responsableId, $idsResponsables, true)) {
            $errores['responsable_id'] = 'Error. Debes seleccionar un responsable válido.';
        }

        if ($valor === '' || !is_numeric($valor) || (int)$valor <0) {
            $errores['valor'] = 'Error. El valor debe ser número positivo.';
        }

        if ($indicaciones === '' ) {
            $errores['indicaciones'] = 'Error. Escribe una breve indicación.';
        }

        if (!empty($errores)) {
            self::view('lead/lead_create_view', [
                'tituloPagina'   => 'PipelineDesk | Nuevo lead',
                'errores'        => $errores,
                'lead_nombre'    => $leadNombre,
                'email'          => $email,
                'telefono'       => $telefono,
                'servicios'      => $servicios,
                'indicaciones'   => $indicaciones,
                'prioridad'      => $prioridad,
                'valor'          => $valor,
                'estado'         => $estado,
                'responsable_id' => $responsableId,
                'mensajeFlash'   => null,
                'iconoFlash'     => null,
                'claseFlash'     => 'error',
                'serviciosLista' => $lm->getServicios(),
                'prioridades'    => $lm->getPrioridades(),
                'estadosLista'   => $lm->getEstados(),
                'responsables'   => $responsables
            ]);
            return;
        }

        $guardado = $lm->create([
            'lead_nombre'     => $leadNombre,
            'estado'          => $estado,
            'responsable_id'  => $responsableId,
            'servicios'       => $servicios,
            'indicaciones'    => $indicaciones !== '' ? $indicaciones : null,
            'lead_score'      => 0,
            'email'           => $email !== '' ? $email : null,
            'telefono'        => $telefono !== '' ? $telefono : null,
            'valor'           => $valor !== '' ? (float) $valor : null,
            'ultimo_contacto' => null,
            'prioridad'       => $prioridad,
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
