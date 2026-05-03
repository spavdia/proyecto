<?php

declare(strict_types=1);

namespace Sergio\App\models;

use Sergio\Lib\Database;

class TareaModel
{
    private Database $db;

    private array $tiposActividad = [
        'Llamada',
        'Email',
        'Cita presencial',
        'Objeciones'
    ];

    private array $estadosTarea = [
        'Pendiente',
        'En curso',
        'Terminada'
    ];

    private array $tiposBloqueo = [
        'Definir',
        'Precio',
        'No me interesa',
        'No tengo tiempo',
        'Tiene que consultarlo',
        'Falta de confianza',
        'No sabe si es su nivel',
        'Ya usa otra solución',
        'No ve utilidad laboral',
        'Dudas sobre el método'
    ];

    private array $solucionesBloqueo = [
        'Definir',
        'Reencuadre de valor',
        'Facilidad y acompañamiento',
        'Prueba o demostración'
    ];

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getTiposActividad(): array
    {
        return $this->tiposActividad;
    }

    public function getEstadosTarea(): array
    {
        return $this->estadosTarea;
    }

    public function getTiposBloqueo(): array
    {
        return $this->tiposBloqueo;
    }

    public function getSolucionesBloqueo(): array
    {
        return $this->solucionesBloqueo;
    }

    public function getUsuariosActivos(): array
    {
        $sql = "SELECT id, nombre, rol
                FROM usuarios
                WHERE activo = 1
                ORDER BY nombre ASC";

        return $this->db->executeQuery($sql);
    }

    public function getLeadsSelect(): array
    {
        $sql = "SELECT id, lead_nombre, estado
                FROM leads
                ORDER BY created_at DESC";

        return $this->db->executeQuery($sql);
    }

    public function leadExists(int $leadId): bool
    {
        return $this->getLeadInfo($leadId) !== null;
    }

    public function usuarioActivoExists(int $usuarioId): bool
    {
        $sql = "SELECT id
                FROM usuarios
                WHERE id = ?
                  AND activo = 1
                LIMIT 1";

        $resultado = $this->db->executeQuery($sql, [$usuarioId]);

        return !empty($resultado);
    }

    public function getLeadInfo(int $leadId): ?array
    {
        $sql = "SELECT id, lead_nombre, estado, responsable_id
                FROM leads
                WHERE id = ?
                LIMIT 1";

        $resultado = $this->db->executeQuery($sql, [$leadId]);

        return $resultado[0] ?? null;
    }

    public function create(array $datos): bool
    {
        $sql = "INSERT INTO tareas_lead (
                    lead_id,
                    usuario_creador_id,
                    usuario_asignado_id,
                    tipo_actividad,
                    tipo_bloqueo,
                    solucion_bloqueo,
                    descripcion,
                    fecha_final,
                    estado,
                    leida_asignado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $resultado = $this->db->executeUpdate($sql, [
            $datos['lead_id'],
            $datos['usuario_creador_id'],
            $datos['usuario_asignado_id'],
            $datos['tipo_actividad'],
            $datos['tipo_bloqueo'] ?? null,
            $datos['solucion_bloqueo'] ?? null,
            $datos['descripcion'],
            $datos['fecha_final'],
            $datos['estado'],
            $datos['leida_asignado'] ?? 0
        ]);

        if ($resultado === false) {
            return false;
        }

        $this->updateUltimoContactoLead((int) $datos['lead_id']);

        return true;
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT
                    t.*,
                    l.lead_nombre,
                    l.estado AS lead_estado,
                    uc.nombre AS creador_nombre,
                    ua.nombre AS asignado_nombre
                FROM tareas_lead t
                INNER JOIN leads l ON l.id = t.lead_id
                INNER JOIN usuarios uc ON uc.id = t.usuario_creador_id
                INNER JOIN usuarios ua ON ua.id = t.usuario_asignado_id
                WHERE t.id = ?
                LIMIT 1";

        $resultado = $this->db->executeQuery($sql, [$id]);

        return $resultado[0] ?? null;
    }

    public function update(int $id, array $datos): bool
    {
        $sql = "UPDATE tareas_lead
                SET descripcion = ?,
                    fecha_final = ?,
                    estado = ?,
                    tipo_bloqueo = ?,
                    solucion_bloqueo = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $resultado = $this->db->executeUpdate($sql, [
            $datos['descripcion'],
            $datos['fecha_final'],
            $datos['estado'],
            $datos['tipo_bloqueo'] ?? null,
            $datos['solucion_bloqueo'] ?? null,
            $id
        ]);

        return $resultado !== false;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM tareas_lead WHERE id = ?";
        $resultado = $this->db->executeUpdate($sql, [$id]);

        return $resultado !== false;
    }

    public function getTareasListado(int $usuarioId, bool $esAdmin = false): array
    {
        $sql = "SELECT
                    t.id,
                    t.lead_id,
                    t.usuario_creador_id,
                    t.usuario_asignado_id,
                    t.tipo_actividad,
                    t.tipo_bloqueo,
                    t.solucion_bloqueo,
                    t.descripcion,
                    t.fecha_final,
                    t.estado,
                    t.leida_asignado,
                    t.created_at,
                    t.updated_at,
                    l.lead_nombre,
                    l.estado AS lead_estado,
                    uc.nombre AS creador_nombre,
                    ua.nombre AS asignado_nombre
                FROM tareas_lead t
                INNER JOIN leads l ON l.id = t.lead_id
                INNER JOIN usuarios uc ON uc.id = t.usuario_creador_id
                INNER JOIN usuarios ua ON ua.id = t.usuario_asignado_id";

        $params = [];

        if (!$esAdmin) {
            $sql .= " WHERE t.usuario_asignado_id = ?";
            $params[] = $usuarioId;
        }

        $sql .= " ORDER BY
                    CASE
                        WHEN t.estado = 'Pendiente' THEN 1
                        WHEN t.estado = 'En curso' THEN 2
                        ELSE 3
                    END,
                    t.fecha_final ASC";

        return $this->db->executeQuery($sql, $params);
    }

    public function getBloqueosResumen(int $usuarioId, bool $esAdmin = false): array
    {
        $sql = "SELECT
                    SUM(CASE WHEN tipo_actividad = 'Objeciones' AND estado IN ('Pendiente', 'En curso') THEN 1 ELSE 0 END) AS abiertos,
                    SUM(CASE WHEN tipo_actividad = 'Objeciones' AND estado = 'Terminada' THEN 1 ELSE 0 END) AS resueltos
                FROM tareas_lead";
        $params = [];

        if (!$esAdmin) {
            $sql .= " WHERE usuario_asignado_id = ?";
            $params[] = $usuarioId;
        }

        $resultado = $this->db->executeQuery($sql, $params);
        $fila = $resultado[0] ?? [];

        $abiertos = (int) ($fila['abiertos'] ?? 0);
        $resueltos = (int) ($fila['resueltos'] ?? 0);
        $total = $abiertos + $resueltos;
        $porcentaje = $total > 0 ? (int) round(($resueltos / $total) * 100) : 0;

        return [
            'abiertos'   => $abiertos,
            'resueltos'  => $resueltos,
            'porcentaje' => $porcentaje,
            'total'      => $total
        ];
    }

    public function getResumenEstados(int $usuarioId, bool $esAdmin = false): array
    {
        $resumen = [
            'Pendiente' => 0,
            'En curso'  => 0,
            'Terminada' => 0
        ];

        $sql = "SELECT estado, COUNT(*) AS total
                FROM tareas_lead";
        $params = [];

        if (!$esAdmin) {
            $sql .= " WHERE usuario_asignado_id = ?";
            $params[] = $usuarioId;
        }

        $sql .= " GROUP BY estado";

        $filas = $this->db->executeQuery($sql, $params);

        foreach ($filas as $fila) {
            $estado = (string) ($fila['estado'] ?? '');
            if (array_key_exists($estado, $resumen)) {
                $resumen[$estado] = (int) ($fila['total'] ?? 0);
            }
        }

        return $resumen;
    }

    public function getRetrasadasCount(int $usuarioId, bool $esAdmin = false): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM tareas_lead
                WHERE DATE(fecha_final) < CURDATE()
                  AND estado <> 'Terminada'";
        $params = [];

        if (!$esAdmin) {
            $sql .= " AND usuario_asignado_id = ?";
            $params[] = $usuarioId;
        }

        $resultado = $this->db->executeQuery($sql, $params);

        return (int) ($resultado[0]['total'] ?? 0);
    }

    public function getProximosSeguimientosByUsuario(int $usuarioId, bool $esAdmin = false, int $limite = 3): array
    {
        $sql = "SELECT
                    t.id,
                    t.tipo_actividad,
                    t.fecha_final,
                    l.lead_nombre
                FROM tareas_lead t
                INNER JOIN leads l ON l.id = t.lead_id
                WHERE t.estado IN ('Pendiente', 'En curso')";
        $params = [];

        if (!$esAdmin) {
            $sql .= " AND t.usuario_asignado_id = ?";
            $params[] = $usuarioId;
        }

        $sql .= " ORDER BY t.fecha_final ASC
                  LIMIT " . (int) $limite;

        return $this->db->executeQuery($sql, $params);
    }

    public function getNuevasAsignadasByUsuario(int $usuarioId, int $limite = 3): array
    {
        $sql = "SELECT
                    t.id,
                    t.tipo_actividad,
                    t.fecha_final,
                    l.lead_nombre
                FROM tareas_lead t
                INNER JOIN leads l ON l.id = t.lead_id
                WHERE t.usuario_asignado_id = ?
                  AND t.leida_asignado = 0
                  AND t.usuario_creador_id <> t.usuario_asignado_id
                ORDER BY t.created_at DESC
                LIMIT " . (int) $limite;

        return $this->db->executeQuery($sql, [$usuarioId]);
    }

    public function markNuevasComoLeidas(int $usuarioId): void
    {
        $sql = "UPDATE tareas_lead
                SET leida_asignado = 1
                WHERE usuario_asignado_id = ?
                  AND leida_asignado = 0";

        $this->db->executeUpdate($sql, [$usuarioId]);
    }

    public function getResumenUsuariosConTareas(): array
    {
        $sql = "SELECT
                    u.id,
                    u.nombre,
                    u.rol,
                    COUNT(t.id) AS total_tareas,
                    SUM(CASE WHEN t.estado = 'Terminada' THEN 1 ELSE 0 END) AS tareas_terminadas
                FROM usuarios u
                INNER JOIN tareas_lead t ON t.usuario_asignado_id = u.id
                GROUP BY u.id, u.nombre, u.rol
                HAVING COUNT(t.id) > 0
                ORDER BY u.nombre ASC";

        $filas = $this->db->executeQuery($sql);

        foreach ($filas as &$fila) {
            $total = (int) ($fila['total_tareas'] ?? 0);
            $terminadas = (int) ($fila['tareas_terminadas'] ?? 0);
            $fila['porcentaje_terminadas'] = $total > 0
                ? (int) round(($terminadas / $total) * 100)
                : 0;
        }

        return $filas;
    }

    public function createObjecionAutomatica(int $leadId, int $usuarioCreadorId): bool
    {
        $lead = $this->getLeadInfo($leadId);

        if (!$lead || (string) ($lead['estado'] ?? '') !== 'Objeciones') {
            return false;
        }

        if ($this->existeObjecionAbiertaPorLead($leadId)) {
            return false;
        }

        $usuarioAsignadoId = (int) ($lead['responsable_id'] ?? 0);
        if ($usuarioAsignadoId <= 0) {
            $usuarioAsignadoId = $usuarioCreadorId;
        }

        return $this->create([
            'lead_id'             => $leadId,
            'usuario_creador_id'  => $usuarioCreadorId,
            'usuario_asignado_id' => $usuarioAsignadoId,
            'tipo_actividad'      => 'Objeciones',
            'tipo_bloqueo'        => 'Definir',
            'solucion_bloqueo'    => 'Definir',
            'descripcion'         => 'Lead en fase de objeciones. Define el bloqueo y propone una solución para avanzar el embudo.',
            'fecha_final'         => date('Y-m-d', strtotime('+3 days')) . ' 00:00:00',
            'estado'              => 'Pendiente',
            'leida_asignado'      => ($usuarioAsignadoId === $usuarioCreadorId) ? 1 : 0
        ]);
    }

    public function existeObjecionAbiertaPorLead(int $leadId): bool
    {
        $sql = "SELECT id
                FROM tareas_lead
                WHERE lead_id = ?
                  AND tipo_actividad = 'Objeciones'
                  AND estado IN ('Pendiente', 'En curso')
                LIMIT 1";

        $resultado = $this->db->executeQuery($sql, [$leadId]);

        return !empty($resultado);
    }

    public function updateUltimoContactoLead(int $leadId, ?string $fecha = null): bool
    {
        $fechaContacto = $fecha ?? date('Y-m-d H:i:s');

        $sql = "UPDATE leads
                SET ultimo_contacto = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $resultado = $this->db->executeUpdate($sql, [$fechaContacto, $leadId]);

        return $resultado !== false;
    }
}