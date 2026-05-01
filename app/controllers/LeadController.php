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

    //metodo GET URL con id o bien llamado con parametro URL id + ?editar=1
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
        $diasEnPanel = $lm->getDiasEnPanel((string)($lead['created_at'] ?? ''));
        $esModoEdicion = isset($_GET['editar']) && $_GET['editar'] === '1';

        self::view('lead/detalles_view', [
            'tituloPagina'   => 'PipelineDesk | Detalle del lead',
            'usuario' => $usuario,
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

    //Actualizar Lead


    //post actualizar lead
    public static function actualizarLead(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $lm = new LeadModel();
        $usuario = SessionManager::get('usuario');
        $usuarioId = (int)($usuario['id'] ?? 0);
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
        $responsableId = (int)($_POST['responsable_id'] ?? USUARIO_POR_DEFECTO);

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
        $idsResponsables = array_map(static fn($r) => (int)$r['id'], $responsables);

        if (!in_array($responsableId, $idsResponsables, true)) {
            $erroresEditar['responsable_id'] = 'Error. Debes seleccionar un responsable válido.';
        }

        if ($valor !== '' && (!is_numeric($valor) || (float)$valor < 0)) {
            $erroresEditar['valor'] = 'Error. El valor debe ser un número positivo.';
        }

        /*
     * CAMPOS SOLO ADMIN
     */
        $leadScore = (string)($leadActual['lead_score'] ?? '0');
        $ultimoContacto = '';
        $indicaciones = (string)($leadActual['indicaciones'] ?? '');
        $origen = (string)($leadActual['origen'] ?? '');
        $createdAt = (string)($leadActual['created_at'] ?? '');

        if ($esAdmin) {
            $leadScore = trim($_POST['lead_score'] ?? (string)($leadActual['lead_score'] ?? '0'));
            $ultimoContacto = trim($_POST['ultimo_contacto'] ?? '');
            $indicaciones = trim($_POST['indicaciones'] ?? '');
            $origen = trim($_POST['origen'] ?? (string)($leadActual['origen'] ?? ''));
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
            'lead_nombre'    => $leadNombre,
            'email'          => $email,
            'telefono'       => $telefono,
            'servicios'      => $servicios,
            'prioridad'      => $prioridad,
            'valor'          => $valor,
            'estado'         => $estado,
            'responsable_id' => $responsableId,
            'lead_score'     => $leadScore,
            'ultimo_contacto' => $ultimoContacto,
            'indicaciones'   => $indicaciones,
            'origen'         => $origen,
            'created_at'     => $createdAt
        ];

        if (!empty($erroresEditar)) {
            $notas = $lm->getNotasByLead($id);
            $historial = $lm->getHistorialByLead($id);
            $diasEnPanel = $lm->getDiasEnPanel((string)($leadActual['created_at'] ?? ''));

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
            'lead_nombre'    => $leadNombre,
            'estado'         => $estado,
            'responsable_id' => $responsableId,
            'servicios'      => $servicios,
            'indicaciones'   => $esAdmin ? ($indicaciones !== '' ? $indicaciones : null) : ($leadActual['indicaciones'] ?? null),
            'lead_score'     => $esAdmin ? (int)$leadScore : (int)($leadActual['lead_score'] ?? 0),
            'email'          => $email !== '' ? $email : null,
            'telefono'       => $telefono !== '' ? $telefono : null,
            'valor'          => $valor !== '' ? (float)$valor : null,
            'ultimo_contacto' => $ultimoContactoBd,
            'prioridad'      => $prioridad,
            'origen'         => $esAdmin ? $origen : (string)($leadActual['origen'] ?? ''),
            'created_at'     => $createdAtBd
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
            'lead_nombre' => 'nombre',
            'email' => 'email',
            'telefono' => 'teléfono',
            'servicios' => 'servicio',
            'valor' => 'valor',
            'prioridad' => 'prioridad',
            'responsable_id' => 'responsable',
            'lead_score' => 'lead score',
            'ultimo_contacto' => 'último contacto',
            'indicaciones' => 'indicaciones',
            'origen' => 'origen',
            'created_at' => 'fecha de creación'
        ];

        foreach ($mapaComparacion as $campo => $texto) {
            $valorAnterior = $leadActual[$campo] ?? null;
            $valorNuevo = $datosUpdate[$campo] ?? null;

            if ((string)$valorAnterior !== (string)$valorNuevo) {
                $camposModificados[] = $texto;
            }
        }

        if ((string)($leadActual['estado'] ?? '') !== $estado) {
            $lm->createHistorial([
                'lead_id'         => $id,
                'usuario_id'      => $usuarioId > 0 ? $usuarioId : null,
                'tipo_evento'     => 'cambio_estado',
                'titulo'          => 'Cambio de estado',
                'descripcion'     => 'El estado se ha actualizado desde la ficha del lead.',
                'estado_anterior' => (string)($leadActual['estado'] ?? ''),
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

        SessionManager::setMensajeFlash(
            'Lead actualizado correctamente.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'leads/' . $id);
        exit();
    }

    //eliminar solo con admin
    public static function eliminarLead(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $rolUsuario = (string)($usuario['rol'] ?? '');

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
}
