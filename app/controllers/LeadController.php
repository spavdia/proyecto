<?php

namespace Sergio\App\Controllers;

use Sergio\Lib\SessionManager;
use Sergio\App\models\LeadModel;

class LeadController extends Controller
{
    public static function mostrarFormContacto(): void
    {
        SessionManager::iniciarSesion();

        $flash = SessionManager::getMensajeFlash();
        $lm = new LeadModel();

        self::view('home/contacto_view', [
            'tituloPagina'   => 'PipelineDesk | Contacto',
            'errores'        => [],
            'lead_nombre'    => '',
            'email'          => '',
            'telefono'       => '',
            'servicios'      => '',
            'indicaciones'   => '',
            'mensajeFlash'   => $flash['mensaje'] ?? null,
            'iconoFlash'     => $flash['icono'] ?? null,
            'claseFlash'     => $flash['clase'] ?? 'info',
            'serviciosLista' => $lm->getServicios()
        ]);
    }

    public static function nuevoContacto(): void
    {
        SessionManager::iniciarSesion();

        $lm = new LeadModel();
        $errores = [];

        $leadNombre = trim($_POST['lead_nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $servicios = trim($_POST['servicios'] ?? '');
        $indicaciones = trim($_POST['indicaciones'] ?? '');

        if ($leadNombre === '') {
            $errores['lead_nombre'] = 'Error. El nombre es obligatorio.';
        }

        if ($email === '') {
            $errores['email'] = 'Error. El email es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Error. Email con formato inválido.';
        }

        if ($telefono === '') {
            $errores['telefono'] = 'Error. El teléfono es obligatorio.';
        }

        if ($servicios === '') {
            $errores['servicios'] = 'Error. Debes seleccionar un servicio.';
        }

        if (!empty($errores)) {
            self::view('home/contacto_view', [
                'tituloPagina'   => 'PipelineDesk | Contacto',
                'errores'        => $errores,
                'lead_nombre'    => $leadNombre,
                'email'          => $email,
                'telefono'       => $telefono,
                'servicios'      => $servicios,
                'indicaciones'   => $indicaciones,
                'mensajeFlash'   => null,
                'iconoFlash'     => null,
                'claseFlash'     => 'error',
                'serviciosLista' => $lm->getServicios()
            ]);
            return;
        }

        $guardado = $lm->create([
            'lead_nombre'  => $leadNombre,
            'servicios'    => $servicios,
            'indicaciones' => $indicaciones !== '' ? $indicaciones : null,
            'lead_score'   => 0,
            'email'        => $email !== '' ? $email : null,
            'telefono'     => $telefono !== '' ? $telefono : null,
            'origen'       => 'formulario_web'
        ]);

        if (!$guardado) {
            self::view('home/contacto_view', [
                'tituloPagina'   => 'PipelineDesk | Contacto',
                'errores'        => [],
                'lead_nombre'    => $leadNombre,
                'email'          => $email,
                'telefono'       => $telefono,
                'servicios'      => $servicios,
                'indicaciones'   => $indicaciones,
                'mensajeFlash'   => 'No se ha podido enviar el formulario. Inténtalo de nuevo.',
                'iconoFlash'     => '⚠',
                'claseFlash'     => 'error',
                'serviciosLista' => $lm->getServicios()
            ]);
            return;
        }

        $leadId = $lm->getUltimoLeadId();

        if ($leadId > 0) {
            $lm->createHistorial([
                'lead_id'         => $leadId,
                'usuario_id'      => null,
                'tipo_evento'     => 'alta',
                'titulo'          => 'Lead recibido desde formulario web',
                'descripcion'     => 'El lead ha entrado desde el formulario público de contacto.',
                'estado_anterior' => null,
                'estado_nuevo'    => ESTADO_POR_DEFECTO
            ]);
        }

        SessionManager::setMensajeFlash(
            'Gracias, hemos recibido tu solicitud correctamente. Te contactaremos en breve.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'contacto');
        exit();
    }

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
            'estado'         => ESTADO_POR_DEFECTO,
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

    public static function nuevoLead(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $lm = new LeadModel();
        $errores = [];

        $usuario = SessionManager::get('usuario');
        $usuarioId = (int)($usuario['id'] ?? 0);

        $leadNombre = trim($_POST['lead_nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $servicios = trim($_POST['servicios'] ?? '');
        $indicaciones = trim($_POST['indicaciones'] ?? '');
        $prioridad = trim($_POST['prioridad'] ?? PRIORIDAD_POR_DEFECTO);
        $valor = trim($_POST['valor'] ?? '');
        $estado = trim($_POST['estado'] ?? ESTADO_POR_DEFECTO);
        $responsableId = (int)($_POST['responsable_id'] ?? USUARIO_POR_DEFECTO);

        if ($leadNombre === '') {
            $errores['lead_nombre'] = 'Error. Debes rellenar el nombre del lead.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Error. El email no tiene un formato válido.';
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
        $idsResponsables = array_map(static fn($r) => (int)$r['id'], $responsables);

        if (!in_array($responsableId, $idsResponsables, true)) {
            $errores['responsable_id'] = 'Error. Debes seleccionar un responsable válido.';
        }

        if ($valor !== '' && (!is_numeric($valor) || (float)$valor < 0)) {
            $errores['valor'] = 'Error. El valor debe ser un número positivo.';
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
            'valor'           => $valor !== '' ? (float)$valor : null,
            'ultimo_contacto' => null,
            'prioridad'       => $prioridad,
            'origen'          => 'app_interna'
        ]);

        if (!$guardado) {
            SessionManager::setMensajeFlash(
                'No se ha podido guardar el lead en bbdd.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'leads/nuevo');
            exit();
        }

        $leadId = $lm->getUltimoLeadId();

        if ($leadId > 0) {
            $lm->createHistorial([
                'lead_id'         => $leadId,
                'usuario_id'      => $usuarioId > 0 ? $usuarioId : null,
                'tipo_evento'     => 'alta',
                'titulo'          => 'Lead creado desde la aplicación',
                'descripcion'     => 'El lead ha sido creado manualmente desde PipelineDesk.',
                'estado_anterior' => null,
                'estado_nuevo'    => $estado
            ]);
        }

        SessionManager::setMensajeFlash(
            'Lead creado correctamente.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'panel');
        exit();
    }

    public static function cambiarEstado(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $usuarioId = (int)($usuario['id'] ?? 0);

        $lm = new LeadModel();
        $estadosValidos = $lm->getEstados();
        $estado = trim($_POST['estado'] ?? '');

        if ($id <= 0 || !in_array($estado, $estadosValidos, true)) {
            SessionManager::setMensajeFlash(
                'Los datos del lead no son válidos.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'panel');
            exit();
        }

        $leadActual = $lm->findById($id);

        if (!$leadActual) {
            SessionManager::setMensajeFlash(
                'No se ha encontrado el lead.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'panel');
            exit();
        }

        $estadoAnterior = (string)($leadActual['estado'] ?? '');
        $actualizado = $lm->updateEstado($id, $estado);

        if (!$actualizado) {
            SessionManager::setMensajeFlash(
                'No se ha podido guardar el cambio de estado en bbdd.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'panel');
            exit();
        }

        if ($estadoAnterior !== $estado) {
            $lm->createHistorial([
                'lead_id'         => $id,
                'usuario_id'      => $usuarioId > 0 ? $usuarioId : null,
                'tipo_evento'     => 'cambio_estado',
                'titulo'          => 'Cambio de estado',
                'descripcion'     => 'El lead ha cambiado de estado.',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo'    => $estado
            ]);
        }

        SessionManager::setMensajeFlash(
            'Estado actualizado.',
            '✅',
            'exito'
        );

        if (($_POST['volver_detalle'] ?? '') === '1') {
            header('Location: ' . BASE_URL . 'leads/' . $id);
            exit();
        }

        header('Location: ' . BASE_URL . 'panel');
        exit();
    }

    public static function mostrarDetalle(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $lm = new LeadModel();
        $flash = SessionManager::getMensajeFlash();

        if ($id <= 0) {
            SessionManager::setMensajeFlash(
                'Lead no válido.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'panel');
            exit();
        }

        $lead = $lm->findById($id);

        if (!$lead) {
            SessionManager::setMensajeFlash(
                'No se ha encontrado el lead.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'panel');
            exit();
        }

        $notas = $lm->getNotasByLead($id);
        $historial = $lm->getHistorialByLead($id);
        $diasEnPanel = $lm->getDiasEnPanel((string)($lead['created_at'] ?? ''));

        self::view('lead/detalles_view', [
            'tituloPagina'  => 'PipelineDesk | Detalle del lead',
            'lead'          => $lead,
            'notas'         => $notas,
            'historial'     => $historial,
            'diasEnPanel'   => $diasEnPanel,
            'estadosEmbudo' => $lm->getEstados(),
            'mensajeFlash'  => $flash['mensaje'] ?? null,
            'iconoFlash'    => $flash['icono'] ?? null,
            'claseFlash'    => $flash['clase'] ?? 'info'
        ]);
    }

    public static function nuevaNota(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $usuarioId = (int)($usuario['id'] ?? 0);
        $tipoActividad = trim($_POST['tipo_actividad'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        $tiposValidos = ['Llamada', 'Email', 'Cita presencial'];

        if ($id <= 0 || $usuarioId <= 0) {
            SessionManager::setMensajeFlash(
                'No se ha podido registrar la actividad.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'panel');
            exit();
        }

        if (!in_array($tipoActividad, $tiposValidos, true) || $contenido === '') {
            SessionManager::setMensajeFlash(
                'Debes seleccionar una actividad y escribir la nota.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'leads/' . $id);
            exit();
        }

        $lm = new LeadModel();
        $lead = $lm->findById($id);

        if (!$lead) {
            SessionManager::setMensajeFlash(
                'No se ha encontrado el lead.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'panel');
            exit();
        }

        $guardada = $lm->createNota([
            'lead_id'        => $id,
            'usuario_id'     => $usuarioId,
            'tipo_actividad' => $tipoActividad,
            'contenido'      => $contenido
        ]);

        if (!$guardada) {
            SessionManager::setMensajeFlash(
                'No se ha podido guardar la actividad.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'leads/' . $id);
            exit();
        }

        $lm->createHistorial([
            'lead_id'         => $id,
            'usuario_id'      => $usuarioId,
            'tipo_evento'     => 'nota',
            'titulo'          => 'Nueva actividad: ' . $tipoActividad,
            'descripcion'     => $contenido,
            'estado_anterior' => null,
            'estado_nuevo'    => null
        ]);

        SessionManager::setMensajeFlash(
            'Actividad guardada correctamente.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'leads/' . $id);
        exit();
    }
}