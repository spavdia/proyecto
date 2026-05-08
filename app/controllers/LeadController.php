<?php

namespace Sergio\App\Controllers;

use Sergio\App\model\LoginModel;
use Sergio\Lib\SessionManager;
use Sergio\App\models\LeadModel;
use Sergio\App\models\TareaModel;

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
        $usuarioId = (int) ($usuario['id'] ?? 0);

        $leadNombre = trim($_POST['lead_nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $servicios = trim($_POST['servicios'] ?? '');
        $indicaciones = trim($_POST['indicaciones'] ?? '');
        $prioridad = trim($_POST['prioridad'] ?? PRIORIDAD_POR_DEFECTO);
        $valor = trim($_POST['valor'] ?? '');
        $estado = trim($_POST['estado'] ?? ESTADO_POR_DEFECTO);
        $responsableId = (int) ($_POST['responsable_id'] ?? USUARIO_POR_DEFECTO);

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
        $idsResponsables = array_map(static fn($r) => (int) $r['id'], $responsables);

        if (!in_array($responsableId, $idsResponsables, true)) {
            $errores['responsable_id'] = 'Error. Debes seleccionar un responsable válido.';
        }

        if ($valor !== '' && (!is_numeric($valor) || (float) $valor < 0)) {
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
            'valor'           => $valor !== '' ? (float) $valor : null,
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

        $mensajeFlash = 'Lead creado correctamente.';
        $iconoFlash = '✅';
        $claseFlash = 'exito';

        if ($leadId > 0 && $estado === 'Objeciones') {
            $tareaCreada = self::crearTareaAutomaticaObjecion($leadId, $usuarioId);

            if ($tareaCreada) {
                $mensajeFlash = 'Lead creado en Objeciones. Debes definir la objeción pendiente desde Tareas.';
                $iconoFlash = '⚠';
                $claseFlash = 'info';
            } else {
                $mensajeFlash = 'Lead creado en Objeciones.';
                $iconoFlash = '⚠';
                $claseFlash = 'info';
            }
        }

        if ($leadId > 0 && $estado === 'Ganado') {
            self::crearNotificacionGanado($leadId, $usuarioId);
            $mensajeFlash = 'Lead cerrado como ganado. Premio conseguido para el equipo.';
            $iconoFlash = '🏆';
            $claseFlash = 'exito';
        }

        SessionManager::setMensajeFlash(
            $mensajeFlash,
            $iconoFlash,
            $claseFlash
        );

        header('Location: ' . BASE_URL . 'panel');
        exit();
    }

    public static function cambiarEstado(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);

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

        $estadoAnterior = (string) ($leadActual['estado'] ?? '');
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

        $mensajeFlash = 'Estado actualizado.';
        $iconoFlash = '✅';
        $claseFlash = 'exito';

        if ($estadoAnterior !== 'Objeciones' && $estado === 'Objeciones') {
            $tareaCreada = self::crearTareaAutomaticaObjecion($id, $usuarioId);

            if ($tareaCreada) {
                $mensajeFlash = 'El lead ha entrado en Objeciones. Debes definir la objeción pendiente desde Tareas.';
                $iconoFlash = '⚠';
                $claseFlash = 'info';
            } else {
                $mensajeFlash = 'El lead ha entrado en Objeciones.';
                $iconoFlash = '⚠';
                $claseFlash = 'info';
            }
        }

        if ($estadoAnterior !== 'Ganado' && $estado === 'Ganado') {
            self::crearNotificacionGanado($id, $usuarioId);
            $mensajeFlash = 'Lead ganado correctamente. Premio conseguido para el equipo.';
            $iconoFlash = '🏆';
            $claseFlash = 'exito';
        }

        SessionManager::setMensajeFlash(
            $mensajeFlash,
            $iconoFlash,
            $claseFlash
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
        $usuario = SessionManager::get('usuario');

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
        $diasEnPanel = $lm->getDiasEnPanel((string) ($lead['created_at'] ?? ''));
        $esModoEdicion = isset($_GET['editar']) && $_GET['editar'] === '1';

        self::view('lead/detalles_view', [
            'tituloPagina'   => 'PipelineDesk | Detalle del lead',
            'usuario'        => $usuario,
            'lead'           => $lead,
            'notas'          => $notas,
            'historial'      => $historial,
            'diasEnPanel'    => $diasEnPanel,
            'estadosEmbudo'  => $lm->getEstados(),
            'estadosLista'   => $lm->getEstados(),
            'prioridades'    => $lm->getPrioridades(),
            'responsables'   => $lm->getResponsables(),
            'serviciosLista' => $lm->getServicios(),
            'esModoEdicion'  => $esModoEdicion,
            'erroresEditar'  => [],
            'leadForm'       => [],
            'mensajeFlash'   => $flash['mensaje'] ?? null,
            'iconoFlash'     => $flash['icono'] ?? null,
            'claseFlash'     => $flash['clase'] ?? 'info'
        ]);
    }

    public static function nuevaNota(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
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

        $lm->updateUltimoContacto($id);

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

    public static function actualizarLead(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $lm = new LeadModel();
        $usuario = SessionManager::get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $esAdmin = (($usuario['rol'] ?? '') === 'admin');

        if ($id <= 0) {
            SessionManager::setMensajeFlash(
                'Lead no válido.',
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

        $erroresEditar = [];

        $leadNombre = trim($_POST['lead_nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $servicios = trim($_POST['servicios'] ?? '');
        $prioridad = trim($_POST['prioridad'] ?? PRIORIDAD_POR_DEFECTO);
        $valor = trim($_POST['valor'] ?? '');
        $estado = trim($_POST['estado'] ?? ESTADO_POR_DEFECTO);
        $responsableId = (int) ($_POST['responsable_id'] ?? USUARIO_POR_DEFECTO);

        if ($leadNombre === '') {
            $erroresEditar['lead_nombre'] = 'Error. Debes rellenar el nombre del lead.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erroresEditar['email'] = 'Error. El email no tiene un formato válido.';
        }

        if ($servicios === '') {
            $erroresEditar['servicios'] = 'Error. Debes seleccionar un servicio.';
        }

        if (!in_array($servicios, $lm->getServicios(), true)) {
            $erroresEditar['servicios'] = 'Error. Debes seleccionar un servicio válido.';
        }

        if (!in_array($prioridad, $lm->getPrioridades(), true)) {
            $erroresEditar['prioridad'] = 'Error. Debes seleccionar una prioridad válida.';
        }

        if (!in_array($estado, $lm->getEstados(), true)) {
            $erroresEditar['estado'] = 'Error. Debes seleccionar un estado válido.';
        }

        $responsables = $lm->getResponsables();
        $idsResponsables = array_map(static fn($r) => (int) $r['id'], $responsables);

        if (!in_array($responsableId, $idsResponsables, true)) {
            $erroresEditar['responsable_id'] = 'Error. Debes seleccionar un responsable válido.';
        }

        if ($valor !== '' && (!is_numeric($valor) || (float) $valor < 0)) {
            $erroresEditar['valor'] = 'Error. El valor debe ser un número positivo.';
        }

        $leadScore = (string) ($leadActual['lead_score'] ?? '0');
        $ultimoContacto = '';
        $indicaciones = (string) ($leadActual['indicaciones'] ?? '');
        $origen = (string) ($leadActual['origen'] ?? '');
        $createdAt = (string) ($leadActual['created_at'] ?? '');

        if ($esAdmin) {
            $leadScore = trim($_POST['lead_score'] ?? (string) ($leadActual['lead_score'] ?? '0'));
            $ultimoContacto = trim($_POST['ultimo_contacto'] ?? '');
            $indicaciones = trim($_POST['indicaciones'] ?? '');
            $origen = trim($_POST['origen'] ?? (string) ($leadActual['origen'] ?? ''));
            $createdAt = trim($_POST['created_at'] ?? '');

            if ($leadScore === '' || !ctype_digit($leadScore)) {
                $erroresEditar['lead_score'] = 'Error. Lead Score debe ser un entero igual o mayor que 0.';
            }

            if ($ultimoContacto !== '') {
                $fechaUltimoContacto = \DateTime::createFromFormat('Y-m-d\TH:i', $ultimoContacto);
                if (!$fechaUltimoContacto) {
                    $erroresEditar['ultimo_contacto'] = 'Error. La fecha de último contacto no es válida.';
                }
            }

            if (!in_array($origen, ['formulario_web', 'app_interna'], true)) {
                $erroresEditar['origen'] = 'Error. Debes seleccionar un origen válido.';
            }

            if ($createdAt === '') {
                $erroresEditar['created_at'] = 'Error. La fecha de creación es obligatoria.';
            } else {
                $fechaCreacion = \DateTime::createFromFormat('Y-m-d\TH:i', $createdAt);
                if (!$fechaCreacion) {
                    $erroresEditar['created_at'] = 'Error. La fecha de creación no es válida.';
                }
            }
        }

        $leadForm = [
            'lead_nombre'     => $leadNombre,
            'email'           => $email,
            'telefono'        => $telefono,
            'servicios'       => $servicios,
            'prioridad'       => $prioridad,
            'valor'           => $valor,
            'estado'          => $estado,
            'responsable_id'  => $responsableId,
            'lead_score'      => $leadScore,
            'ultimo_contacto' => $ultimoContacto,
            'indicaciones'    => $indicaciones,
            'origen'          => $origen,
            'created_at'      => $createdAt
        ];

        if (!empty($erroresEditar)) {
            $notas = $lm->getNotasByLead($id);
            $historial = $lm->getHistorialByLead($id);
            $diasEnPanel = $lm->getDiasEnPanel((string) ($leadActual['created_at'] ?? ''));

            self::view('lead/detalles_view', [
                'tituloPagina'   => 'PipelineDesk | Detalle del lead',
                'usuario'        => $usuario,
                'lead'           => $leadActual,
                'notas'          => $notas,
                'historial'      => $historial,
                'diasEnPanel'    => $diasEnPanel,
                'estadosEmbudo'  => $lm->getEstados(),
                'estadosLista'   => $lm->getEstados(),
                'prioridades'    => $lm->getPrioridades(),
                'responsables'   => $responsables,
                'serviciosLista' => $lm->getServicios(),
                'esModoEdicion'  => true,
                'erroresEditar'  => $erroresEditar,
                'leadForm'       => $leadForm,
                'mensajeFlash'   => null,
                'iconoFlash'     => null,
                'claseFlash'     => 'error'
            ]);
            return;
        }

        $ultimoContactoBd = $leadActual['ultimo_contacto'] ?? null;
        $createdAtBd = $leadActual['created_at'] ?? null;

        if ($esAdmin) {
            $ultimoContactoBd = $ultimoContacto !== '' ? str_replace('T', ' ', $ultimoContacto) . ':00' : null;
            $createdAtBd = str_replace('T', ' ', $createdAt) . ':00';
        }

        $datosUpdate = [
            'lead_nombre'     => $leadNombre,
            'estado'          => $estado,
            'responsable_id'  => $responsableId,
            'servicios'       => $servicios,
            'indicaciones'    => $esAdmin ? ($indicaciones !== '' ? $indicaciones : null) : ($leadActual['indicaciones'] ?? null),
            'lead_score'      => $esAdmin ? (int) $leadScore : (int) ($leadActual['lead_score'] ?? 0),
            'email'           => $email !== '' ? $email : null,
            'telefono'        => $telefono !== '' ? $telefono : null,
            'valor'           => $valor !== '' ? (float) $valor : null,
            'ultimo_contacto' => $ultimoContactoBd,
            'prioridad'       => $prioridad,
            'origen'          => $esAdmin ? $origen : (string) ($leadActual['origen'] ?? ''),
            'created_at'      => $createdAtBd
        ];

        $actualizado = $lm->update($id, $datosUpdate);

        if (!$actualizado) {
            SessionManager::setMensajeFlash(
                'No se ha podido actualizar el lead en bbdd.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'leads/' . $id . '?editar=1');
            exit();
        }

        $camposModificados = [];

        $mapaComparacion = [
            'lead_nombre'     => 'nombre',
            'email'           => 'email',
            'telefono'        => 'teléfono',
            'servicios'       => 'servicio',
            'valor'           => 'valor',
            'prioridad'       => 'prioridad',
            'responsable_id'  => 'responsable',
            'lead_score'      => 'lead score',
            'ultimo_contacto' => 'último contacto',
            'indicaciones'    => 'indicaciones',
            'origen'          => 'origen',
            'created_at'      => 'fecha de creación'
        ];

        foreach ($mapaComparacion as $campo => $texto) {
            $valorAnterior = $leadActual[$campo] ?? null;
            $valorNuevo = $datosUpdate[$campo] ?? null;

            if ((string) $valorAnterior !== (string) $valorNuevo) {
                $camposModificados[] = $texto;
            }
        }

        if ((string) ($leadActual['estado'] ?? '') !== $estado) {
            $lm->createHistorial([
                'lead_id'         => $id,
                'usuario_id'      => $usuarioId > 0 ? $usuarioId : null,
                'tipo_evento'     => 'cambio_estado',
                'titulo'          => 'Cambio de estado',
                'descripcion'     => 'El estado se ha actualizado desde la ficha del lead.',
                'estado_anterior' => (string) ($leadActual['estado'] ?? ''),
                'estado_nuevo'    => $estado
            ]);
        }

        if (!empty($camposModificados)) {
            $descripcion = 'Se han actualizado estos campos: ' . implode(', ', $camposModificados) . '.';

            $lm->createHistorial([
                'lead_id'         => $id,
                'usuario_id'      => $usuarioId > 0 ? $usuarioId : null,
                'tipo_evento'     => 'nota',
                'titulo'          => 'Lead actualizado',
                'descripcion'     => $descripcion,
                'estado_anterior' => null,
                'estado_nuevo'    => null
            ]);
        }

        $mensajeFlash = 'Lead actualizado correctamente.';
        $iconoFlash = '✅';
        $claseFlash = 'exito';

        if ((string) ($leadActual['estado'] ?? '') !== 'Objeciones' && $estado === 'Objeciones') {
            $tareaCreada = self::crearTareaAutomaticaObjecion($id, $usuarioId);

            if ($tareaCreada) {
                $mensajeFlash = 'Lead actualizado y enviado a Objeciones. Debes definir la objeción pendiente desde Tareas.';
                $iconoFlash = '⚠';
                $claseFlash = 'info';
            } else {
                $mensajeFlash = 'Lead actualizado y enviado a Objeciones.';
                $iconoFlash = '⚠';
                $claseFlash = 'info';
            }
        }

        if ((string) ($leadActual['estado'] ?? '') !== 'Ganado' && $estado === 'Ganado') {
            self::crearNotificacionGanado($id, $usuarioId);
            $mensajeFlash = 'Lead actualizado y marcado como ganado. Premio conseguido.';
            $iconoFlash = '🏆';
            $claseFlash = 'exito';
        }

        SessionManager::setMensajeFlash(
            $mensajeFlash,
            $iconoFlash,
            $claseFlash
        );

        header('Location: ' . BASE_URL . 'leads/' . $id);
        exit();
    }

    public static function eliminarLead(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $rolUsuario = (string) ($usuario['rol'] ?? '');

        if ($rolUsuario !== 'admin') {
            SessionManager::setMensajeFlash(
                'No tienes permisos para eliminar leads.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'panel');
            exit();
        }

        $lm = new LeadModel();

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

        $eliminado = $lm->delete($id);

        if (!$eliminado) {
            SessionManager::setMensajeFlash(
                'No se ha podido eliminar el lead.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'leads/' . $id);
            exit();
        }

        SessionManager::setMensajeFlash(
            'Lead eliminado correctamente.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'panel');
        exit();
    }

    public static function cambiarEstadoKanban(): void
    {
        SessionManager::iniciarSesion();

        header('Content-Type: application/json; charset=utf-8');

        if (!SessionManager::get('usuario')) {
            http_response_code(401);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Debes iniciar sesión.'
            ]);
            exit();
        }

        $usuario = SessionManager::get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);

        $leadId = (int) ($_POST['lead_id'] ?? 0);
        $estadoNuevo = trim($_POST['estado'] ?? '');

        $lm = new LeadModel();
        $estadosValidos = $lm->getEstados();

        if ($leadId <= 0 || !in_array($estadoNuevo, $estadosValidos, true)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Los datos enviados no son válidos.'
            ]);
            exit();
        }

        $leadActual = $lm->findById($leadId);

        if (!$leadActual) {
            http_response_code(404);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No se ha encontrado el lead.'
            ]);
            exit();
        }

        $estadoAnterior = (string) ($leadActual['estado'] ?? '');

        if ($estadoAnterior === $estadoNuevo) {
            echo json_encode([
                'ok' => true,
                'mensaje' => 'El lead ya estaba en ese estado.',
                'estadoAnterior' => $estadoAnterior,
                'estadoNuevo' => $estadoNuevo
            ]);
            exit();
        }

        $actualizado = $lm->updateEstado($leadId, $estadoNuevo);

        if (!$actualizado) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No se ha podido actualizar el estado.'
            ]);
            exit();
        }

        $lm->createHistorial([
            'lead_id'         => $leadId,
            'usuario_id'      => $usuarioId > 0 ? $usuarioId : null,
            'tipo_evento'     => 'cambio_estado',
            'titulo'          => 'Cambio de estado desde pipeline',
            'descripcion'     => 'El lead ha cambiado de estado desde la vista Kanban.',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $estadoNuevo
        ]);

        $mensajeRespuesta = 'Estado actualizado correctamente.';

        if ($estadoAnterior !== 'Objeciones' && $estadoNuevo === 'Objeciones') {
            $tareaCreada = self::crearTareaAutomaticaObjecion($leadId, $usuarioId);

            if ($tareaCreada) {
                $mensajeRespuesta = 'Lead en Objeciones. Debes definir la objeción pendiente desde Tareas.';
            } else {
                $mensajeRespuesta = 'Lead en Objeciones.';
            }
        }

        if ($estadoAnterior !== 'Ganado' && $estadoNuevo === 'Ganado') {
            self::crearNotificacionGanado($leadId, $usuarioId);
            $mensajeRespuesta = 'Lead ganado. Premio conseguido para el equipo.';
        }

        echo json_encode([
            'ok' => true,
            'mensaje' => $mensajeRespuesta,
            'estadoAnterior' => $estadoAnterior,
            'estadoNuevo' => $estadoNuevo,
            'leadId' => $leadId
        ]);
        exit();
    }

    private static function crearTareaAutomaticaObjecion(int $leadId, int $usuarioId): bool
    {
        $tm = new TareaModel();
        return $tm->createObjecionAutomatica($leadId, $usuarioId);
    }

    //Contactos
    public static function mostrarListado(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $flash = SessionManager::getMensajeFlash();
        $usuario = SessionManager::get('usuario');
        $usuario = is_array($usuario) ? $usuario : [];

        $usuarioId = (int) ($usuario['id'] ?? 0);
        $esAdmin = (($usuario['rol'] ?? '') === 'admin');

        $lm = new LeadModel();

        $usuariosLista = $lm->getResponsables();
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

        if ($filtros['fecha_desde'] !== '' && !self::fechaFiltroValida($filtros['fecha_desde'])) {
            $filtros['fecha_desde'] = '';
        }

        if ($filtros['fecha_hasta'] !== '' && !self::fechaFiltroValida($filtros['fecha_hasta'])) {
            $filtros['fecha_hasta'] = '';
        }

        if (
            $filtros['fecha_desde'] !== ''
            && $filtros['fecha_hasta'] !== ''
            && $filtros['fecha_desde'] > $filtros['fecha_hasta']
        ) {
            [$filtros['fecha_desde'], $filtros['fecha_hasta']] = [$filtros['fecha_hasta'], $filtros['fecha_desde']];
        }

        $leadListados = $lm->getListadoFiltrado($usuarioId, $esAdmin, $filtros);

        self::view('home/listado_view', [
            'leadListados'   => is_array($leadListados) ? $leadListados : [],
            'filtros'        => is_array($filtros) ? $filtros : [],
            'usuariosLista'  => is_array($usuariosLista) ? $usuariosLista : [],
            'serviciosLista' => is_array($serviciosValidos) ? $serviciosValidos : [],
            'estadosLista'   => is_array($estadosValidos) ? $estadosValidos : [],
            'tituloPagina'   => 'PipelineDesk | Listado',
            'usuario'        => $usuario,
            'mensajeFlash'   => $flash['mensaje'] ?? null,
            'iconoFlash'     => $flash['icono'] ?? null,
            'claseFlash'     => $flash['clase'] ?? 'info'
        ]);
    }

    private static function fechaFiltroValida(string $fecha): bool
    {
        $fechaObj = \DateTime::createFromFormat('Y-m-d', $fecha);

        return $fechaObj instanceof \DateTime && $fechaObj->format('Y-m-d') === $fecha;
    }


    private static function crearNotificacionGanado(int $leadId, int $usuarioId): bool
    {
        $lm = new LeadModel();
        return $lm->createGanadoNotificationForAll($leadId, $usuarioId);
    }


    /**FILTROS por BBDD :
     * 
     * 
     * curso  
        mes  
        responsable_id  
        estado  

        CAMPOS
        id  
        lead_nombre  
        estado  
        servicios  
        responsable_nombre  
        ultimo_contacto  
        valor  

     */
}
