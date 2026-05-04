<?php

declare(strict_types=1);

namespace Sergio\App\Controllers;

use DateTime;
use Sergio\Lib\SessionManager;
use Sergio\App\models\LeadModel;
use Sergio\App\models\TareaModel;

class TareaController extends Controller
{
    public static function index(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        self::renderIndex();
    }

    public static function guardar(): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);

        $tm = new TareaModel();

        $errores = [];
        $leadId = (int) ($_POST['lead_id'] ?? 0);
        $asignadoId = (int) ($_POST['usuario_asignado_id'] ?? 0);
        $tipoActividad = trim($_POST['tipo_actividad'] ?? '');
        $tipoBloqueo = trim($_POST['tipo_bloqueo'] ?? 'Definir');
        $solucionBloqueo = trim($_POST['solucion_bloqueo'] ?? 'Definir');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $fechaFinal = trim($_POST['fecha_final'] ?? '');
        $estado = trim($_POST['estado'] ?? 'Pendiente');

        $leadInfo = $tm->getLeadInfo($leadId);

        if ($leadId <= 0 || !$leadInfo) {
            $errores['lead_id'] = 'Error. Debes seleccionar un lead válido.';
        }

        if ($asignadoId <= 0 || !$tm->usuarioActivoExists($asignadoId)) {
            $errores['usuario_asignado_id'] = 'Error. Debes seleccionar un usuario válido.';
        }

        if (!in_array($tipoActividad, $tm->getTiposActividad(), true)) {
            $errores['tipo_actividad'] = 'Error. Debes seleccionar una actividad válida.';
        }

        if ($tipoActividad !== 'Objeciones' && $descripcion === '') {
            $errores['descripcion'] = 'Error. Debes escribir la nota de la tarea.';
        }

        if ($fechaFinal === '') {
            $errores['fecha_final'] = 'Error. Debes seleccionar una fecha final.';
        } else {
            $fecha = DateTime::createFromFormat('Y-m-d', $fechaFinal);
            if (!$fecha) {
                $errores['fecha_final'] = 'Error. La fecha final no es válida.';
            }
        }

        if (!in_array($estado, $tm->getEstadosTarea(), true)) {
            $errores['estado'] = 'Error. Debes seleccionar un estado válido.';
        }

        if ($tipoActividad === 'Objeciones') {
            if (!$leadInfo || (string) ($leadInfo['estado'] ?? '') !== 'Objeciones') {
                $errores['tipo_actividad'] = 'Error. Solo puedes crear una tarea de objeciones para leads que estén en etapa Objeciones.';
            }

            if (!in_array($tipoBloqueo, $tm->getTiposBloqueo(), true)) {
                $errores['tipo_bloqueo'] = 'Error. Debes seleccionar un tipo de bloqueo válido.';
            }

            if (!in_array($solucionBloqueo, $tm->getSolucionesBloqueo(), true)) {
                $errores['solucion_bloqueo'] = 'Error. Debes seleccionar una solución válida.';
            }
        } else {
            $tipoBloqueo = null;
            $solucionBloqueo = null;
        }

        $datosForm = [
            'lead_id'             => $leadId,
            'usuario_asignado_id' => $asignadoId,
            'tipo_actividad'      => $tipoActividad,
            'tipo_bloqueo'        => $tipoBloqueo ?? 'Definir',
            'solucion_bloqueo'    => $solucionBloqueo ?? 'Definir',
            'descripcion'         => $descripcion,
            'fecha_final'         => $fechaFinal,
            'estado'              => $estado
        ];

        if (!empty($errores)) {
            self::renderIndex([
                'mostrarFormulario' => true,
                'errores'           => $errores,
                'datosForm'         => $datosForm
            ]);
            return;
        }

        $descripcionFinal = self::resolverDescripcionTarea(
            $tipoActividad,
            $tipoBloqueo,
            $solucionBloqueo,
            $descripcion
        );

        $fechaFinalBd = $fechaFinal . ' 00:00:00';

        $guardada = $tm->create([
            'lead_id'             => $leadId,
            'usuario_creador_id'  => $usuarioId,
            'usuario_asignado_id' => $asignadoId,
            'tipo_actividad'      => $tipoActividad,
            'tipo_bloqueo'        => $tipoBloqueo,
            'solucion_bloqueo'    => $solucionBloqueo,
            'descripcion'         => $descripcionFinal,
            'fecha_final'         => $fechaFinalBd,
            'estado'              => $estado,
            'leida_asignado'      => ($usuarioId === $asignadoId) ? 1 : 0
        ]);

        if (!$guardada) {
            SessionManager::setMensajeFlash(
                'No se ha podido guardar la tarea.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'tareas');
            exit();
        }

        $descripcionHistorial = 'Se ha creado una tarea de tipo ' . $tipoActividad . ' con fecha final ' . $fechaFinal . '.';
        if ($tipoActividad === 'Objeciones') {
            $descripcionHistorial .= ' Bloqueo: ' . $tipoBloqueo . '. Solución: ' . $solucionBloqueo . '.';
        }

        $leadModel = new LeadModel();
        $leadModel->createHistorial([
            'lead_id'         => $leadId,
            'usuario_id'      => $usuarioId,
            'tipo_evento'     => 'nota',
            'titulo'          => 'Tarea creada',
            'descripcion'     => $descripcionHistorial,
            'estado_anterior' => null,
            'estado_nuevo'    => null
        ]);

        SessionManager::setMensajeFlash(
            'Tarea creada correctamente.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'tareas');
        exit();
    }

    public static function actualizar(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $esAdmin = (($usuario['rol'] ?? '') === 'admin');

        $tm = new TareaModel();
        $tarea = $tm->findById($id);

        if (!$tarea) {
            SessionManager::setMensajeFlash(
                'No se ha encontrado la tarea.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'tareas');
            exit();
        }

        if (!self::puedeGestionarTarea($tarea, $usuarioId, $esAdmin)) {
            SessionManager::setMensajeFlash(
                'No tienes permiso para actualizar esta tarea.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'tareas');
            exit();
        }

        $erroresEdicion = [];
        $descripcion = trim($_POST['descripcion'] ?? '');
        $fechaFinal = trim($_POST['fecha_final'] ?? '');
        $estado = trim($_POST['estado'] ?? '');
        $tipoBloqueo = trim($_POST['tipo_bloqueo'] ?? 'Definir');
        $solucionBloqueo = trim($_POST['solucion_bloqueo'] ?? 'Definir');

        if ((string) ($tarea['tipo_actividad'] ?? '') !== 'Objeciones' && $descripcion === '') {
            $erroresEdicion['descripcion'] = 'Error. Debes escribir la nota.';
        }

        if ($fechaFinal === '') {
            $erroresEdicion['fecha_final'] = 'Error. Debes seleccionar una fecha final.';
        } else {
            $fecha = DateTime::createFromFormat('Y-m-d', $fechaFinal);
            if (!$fecha) {
                $erroresEdicion['fecha_final'] = 'Error. La fecha final no es válida.';
            }
        }

        if (!in_array($estado, $tm->getEstadosTarea(), true)) {
            $erroresEdicion['estado'] = 'Error. Debes seleccionar un estado válido.';
        }

        if ((string) ($tarea['tipo_actividad'] ?? '') === 'Objeciones') {
            if (!in_array($tipoBloqueo, $tm->getTiposBloqueo(), true)) {
                $erroresEdicion['tipo_bloqueo'] = 'Error. Debes seleccionar un bloqueo válido.';
            }

            if (!in_array($solucionBloqueo, $tm->getSolucionesBloqueo(), true)) {
                $erroresEdicion['solucion_bloqueo'] = 'Error. Debes seleccionar una solución válida.';
            }
        } else {
            $tipoBloqueo = null;
            $solucionBloqueo = null;
        }

        $datosEdicion = [
            'descripcion'      => $descripcion,
            'fecha_final'      => $fechaFinal,
            'estado'           => $estado,
            'tipo_bloqueo'     => $tipoBloqueo ?? 'Definir',
            'solucion_bloqueo' => $solucionBloqueo ?? 'Definir'
        ];

        if (!empty($erroresEdicion)) {
            self::renderIndex([
                'editarId'       => $id,
                'erroresEdicion' => $erroresEdicion,
                'datosEdicion'   => $datosEdicion
            ]);
            return;
        }

        $descripcionFinal = self::resolverDescripcionTarea(
            (string) ($tarea['tipo_actividad'] ?? ''),
            $tipoBloqueo,
            $solucionBloqueo,
            $descripcion
        );

        $fechaFinalBd = $fechaFinal . ' 00:00:00';

        $actualizada = $tm->update($id, [
            'descripcion'      => $descripcionFinal,
            'fecha_final'      => $fechaFinalBd,
            'estado'           => $estado,
            'tipo_bloqueo'     => $tipoBloqueo,
            'solucion_bloqueo' => $solucionBloqueo
        ]);

        if (!$actualizada) {
            SessionManager::setMensajeFlash(
                'No se ha podido actualizar la tarea.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'tareas?editar=' . $id);
            exit();
        }

        $descripcionHistorial = 'Se ha actualizado una tarea del lead. Estado: ' . $estado . '.';
        if ((string) ($tarea['tipo_actividad'] ?? '') === 'Objeciones') {
            $descripcionHistorial .= ' Bloqueo: ' . ($tipoBloqueo ?? 'Definir') . '. Solución: ' . ($solucionBloqueo ?? 'Definir') . '.';
        }

        $leadModel = new LeadModel();
        $leadModel->createHistorial([
            'lead_id'         => (int) $tarea['lead_id'],
            'usuario_id'      => $usuarioId,
            'tipo_evento'     => 'nota',
            'titulo'          => 'Tarea actualizada',
            'descripcion'     => $descripcionHistorial,
            'estado_anterior' => null,
            'estado_nuevo'    => null
        ]);

        SessionManager::setMensajeFlash(
            'Tarea actualizada correctamente.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'tareas');
        exit();
    }

    public static function eliminar(int $id): void
    {
        SessionManager::iniciarSesion();
        SessionManager::usuarioNoAutenticado('usuario', 'login');

        $usuario = SessionManager::get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $esAdmin = (($usuario['rol'] ?? '') === 'admin');

        $tm = new TareaModel();
        $tarea = $tm->findById($id);

        if (!$tarea) {
            SessionManager::setMensajeFlash(
                'No se ha encontrado la tarea.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'tareas');
            exit();
        }

        if (!self::puedeGestionarTarea($tarea, $usuarioId, $esAdmin)) {
            SessionManager::setMensajeFlash(
                'No tienes permiso para eliminar esta tarea.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'tareas');
            exit();
        }

        $eliminada = $tm->delete($id);

        if (!$eliminada) {
            SessionManager::setMensajeFlash(
                'No se ha podido eliminar la tarea.',
                '⚠',
                'error'
            );
            header('Location: ' . BASE_URL . 'tareas');
            exit();
        }

        $leadModel = new LeadModel();
        $leadModel->createHistorial([
            'lead_id'         => (int) $tarea['lead_id'],
            'usuario_id'      => $usuarioId,
            'tipo_evento'     => 'nota',
            'titulo'          => 'Tarea eliminada',
            'descripcion'     => 'Se ha eliminado una tarea de tipo ' . (string) $tarea['tipo_actividad'] . '.',
            'estado_anterior' => null,
            'estado_nuevo'    => null
        ]);

        SessionManager::setMensajeFlash(
            'Tarea eliminada correctamente.',
            '✅',
            'exito'
        );

        header('Location: ' . BASE_URL . 'tareas');
        exit();
    }

    private static function puedeGestionarTarea(array $tarea, int $usuarioId, bool $esAdmin): bool
    {
        if ($esAdmin) {
            return true;
        }

        return (int) ($tarea['usuario_asignado_id'] ?? 0) === $usuarioId
            || (int) ($tarea['usuario_creador_id'] ?? 0) === $usuarioId;
    }

    private static function resolverDescripcionTarea(
        string $tipoActividad,
        ?string $tipoBloqueo,
        ?string $solucionBloqueo,
        string $descripcionManual
    ): string {
        if ($tipoActividad !== 'Objeciones') {
            return $descripcionManual;
        }

        if ($solucionBloqueo === null || $solucionBloqueo === '' || $solucionBloqueo === 'Definir') {
            return 'Lead en fase de objeciones. Define el bloqueo y propone una solución para avanzar el embudo.';
        }

        return 'INFO: ' . self::getTextoInfoSolucion($solucionBloqueo, $tipoBloqueo ?? 'Definir');
    }

    private static function getTextoInfoSolucion(string $solucionBloqueo, string $tipoBloqueo): string
    {
        switch ($solucionBloqueo) {
            case 'Reencuadre de valor':
                return 'Relaciona el servicio con el bloqueo "' . $tipoBloqueo . '". Explica el beneficio clave y el coste de no avanzar. Cierra con una propuesta clara.';

            case 'Facilidad y acompañamiento':
                return 'Reduce la fricción del bloqueo "' . $tipoBloqueo . '". Ofrece un paso simple, apoyo cercano y una vía cómoda para continuar. Confirma el siguiente paso hoy.';

            case 'Prueba o demostración':
                return 'Valida la solución ante el bloqueo "' . $tipoBloqueo . '". Muestra un ejemplo breve o una prueba concreta. Pide decisión después de la demostración.';

            default:
                return 'Define el bloqueo real. Habla con el lead y concreta qué necesita para avanzar.';
        }
    }

    private static function renderIndex(array $extra = []): void
    {
        $usuario = SessionManager::get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $esAdmin = (($usuario['rol'] ?? '') === 'admin');

        $tm = new TareaModel();
        $flash = SessionManager::getMensajeFlash();

        $tareas = $tm->getTareasListado($usuarioId, $esAdmin);
        $usuarios = $tm->getUsuariosActivos();
        $leads = $tm->getLeadsSelect();

        $nuevasAsignadas = $tm->getNuevasAsignadasByUsuario($usuarioId);
        if (!empty($nuevasAsignadas)) {
            $tm->markNuevasComoLeidas($usuarioId);
        }

        $bloqueosResumen = $tm->getBloqueosResumen($usuarioId, $esAdmin);
        $resumenEstados = $tm->getResumenEstados($usuarioId, $esAdmin);
        $retrasadasCount = $tm->getRetrasadasCount($usuarioId, $esAdmin);
        $resumenUsuariosAdmin = $esAdmin ? $tm->getResumenUsuariosConTareas() : [];
        $proximosSeguimientos = $tm->getProximosSeguimientosByUsuario($usuarioId, $esAdmin, 3);

        self::view('home/tareas_view', [
            'tituloPagina'         => 'PipelineDesk | Tareas',
            'usuario'              => $usuario,
            'tareas'               => $tareas,
            'usuarios'             => $usuarios,
            'leads'                => $leads,
            'tiposActividad'       => $tm->getTiposActividad(),
            'tiposBloqueo'         => $tm->getTiposBloqueo(),
            'solucionesBloqueo'    => $tm->getSolucionesBloqueo(),
            'estadosTarea'         => $tm->getEstadosTarea(),
            'mostrarFormulario'    => $extra['mostrarFormulario'] ?? false,
            'errores'              => $extra['errores'] ?? [],
            'datosForm'            => $extra['datosForm'] ?? [],
            'editarId'             => $extra['editarId'] ?? (int) ($_GET['editar'] ?? 0),
            'erroresEdicion'       => $extra['erroresEdicion'] ?? [],
            'datosEdicion'         => $extra['datosEdicion'] ?? [],
            'nuevasAsignadas'      => $nuevasAsignadas,
            'bloqueosResumen'      => $bloqueosResumen,
            'resumenEstados'       => $resumenEstados,
            'retrasadasCount'      => $retrasadasCount,
            'resumenUsuariosAdmin' => $resumenUsuariosAdmin,
            'proximosSeguimientos' => $proximosSeguimientos,
            'mensajeFlash'         => $flash['mensaje'] ?? null,
            'iconoFlash'           => $flash['icono'] ?? null,
            'claseFlash'           => $flash['clase'] ?? 'info'
        ]);
    }
}