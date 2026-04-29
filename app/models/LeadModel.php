<?php

namespace Sergio\App\models;

use Sergio\Lib\Database;

class LeadModel
{
    private Database $db;
    private int $ultimoLeadId = 0;

    private array $estados = [
        'Nuevo Lead',
        'Contactado',
        'En Progreso',
        'Objeciones',
        'Ganado',
        'Perdido'
    ];

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getEstados(): array
    {
        return $this->estados;
    }

    public function getServicios(): array
    {
        return [
            'B1 Inglés',
            'B2 Inglés',
            'Informática',
            'Apoyo Primaria',
            'Apoyo Secundaria',
            'Apoyo Bach',
            'Apoyo Univ',
            'Acceso a GS',
            'Selectividad',
            'Acceso Univ+25'
        ];
    }

    public function getResponsables(): array
    {
        $sql = "SELECT id, nombre
                FROM usuarios
                WHERE activo = 1
                  AND rol = 'ventas'
                ORDER BY nombre ASC";

        return $this->db->executeQuery($sql, []);
    }

    public function getPrioridades(): array
    {
        return ['Baja', 'Media', 'Alta'];
    }

    public function getUltimoLeadId(): int
    {
        return $this->ultimoLeadId;
    }

    public function create(array $datos): bool
    {
        $this->ultimoLeadId = 0;

        $estado = $datos['estado'] ?? ESTADO_POR_DEFECTO;
        $responsableId = $datos['responsable_id'] ?? USUARIO_POR_DEFECTO;
        $indicaciones = $datos['indicaciones'] ?? null;
        $leadScore = $datos['lead_score'] ?? 0;
        $email = $datos['email'] ?? null;
        $telefono = $datos['telefono'] ?? null;
        $valor = $datos['valor'] ?? null;
        $ultimoContacto = $datos['ultimo_contacto'] ?? null;
        $prioridad = $datos['prioridad'] ?? PRIORIDAD_POR_DEFECTO;

        $sql = "INSERT INTO leads (
                    lead_nombre,
                    estado,
                    responsable_id,
                    servicios,
                    indicaciones,
                    lead_score,
                    email,
                    telefono,
                    valor,
                    ultimo_contacto,
                    prioridad,
                    origen
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $resultado = $this->db->executeUpdate($sql, [
            $datos['lead_nombre'],
            $estado,
            $responsableId,
            $datos['servicios'],
            $indicaciones,
            $leadScore,
            $email,
            $telefono,
            $valor,
            $ultimoContacto,
            $prioridad,
            $datos['origen']
        ]);

        if ($resultado === false) {
            return false;
        }

        $consultaId = $this->db->executeQuery(
            "SELECT LAST_INSERT_ID() AS ultimo_id",
            []
        );

        if (!empty($consultaId) && isset($consultaId[0]['ultimo_id'])) {
            $this->ultimoLeadId = (int) $consultaId[0]['ultimo_id'];
        }

        return true;
    }

    public function obtenerAgrupadosPorEstado(): array
    {
        $sql = "SELECT
                    l.id,
                    l.lead_nombre,
                    l.estado,
                    l.servicios,
                    l.indicaciones,
                    l.lead_score,
                    l.email,
                    l.telefono,
                    l.valor,
                    l.ultimo_contacto,
                    l.prioridad,
                    l.origen,
                    l.created_at,
                    l.updated_at,
                    u.nombre AS responsable_nombre
                FROM leads l
                LEFT JOIN usuarios u ON l.responsable_id = u.id
                ORDER BY l.created_at DESC";

        $resultados = $this->db->executeQuery($sql, []);
        $agrupados = [];

        foreach ($this->estados as $estado) {
            $agrupados[$estado] = [];
        }

        foreach ($resultados as $lead) {
            $agrupados[$lead['estado']][] = $lead;
        }

        return $agrupados;
    }

    public function updateEstado(int $id, string $estado): bool
    {
        $sql = "UPDATE leads
                SET estado = ?, updated_at = NOW()
                WHERE id = ?";

        $resultado = $this->db->executeUpdate($sql, [$estado, $id]);

        return $resultado !== false;
    }

    public function findById(int $leadId): ?array
    {
        $sql = "SELECT
                    l.*,
                    u.nombre AS responsable_nombre
                FROM leads l
                LEFT JOIN usuarios u ON l.responsable_id = u.id
                WHERE l.id = ?
                LIMIT 1";

        $resultado = $this->db->executeQuery($sql, [$leadId]);

        return $resultado[0] ?? null;
    }

    public function getNotasByLead(int $leadId): array
    {
        $sql = "SELECT
                    n.id,
                    n.tipo_actividad,
                    n.contenido,
                    n.created_at,
                    u.nombre AS usuario_nombre
                FROM notas_lead n
                INNER JOIN usuarios u ON n.usuario_id = u.id
                WHERE n.lead_id = ?
                ORDER BY n.created_at DESC";

        return $this->db->executeQuery($sql, [$leadId]);
    }

    public function getHistorialByLead(int $leadId): array
    {
        $sql = "SELECT
                    h.id,
                    h.tipo_evento,
                    h.titulo,
                    h.descripcion,
                    h.estado_anterior,
                    h.estado_nuevo,
                    h.created_at,
                    u.nombre AS usuario_nombre
                FROM historial_lead h
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                WHERE h.lead_id = ?
                ORDER BY h.created_at DESC, h.id DESC";

        return $this->db->executeQuery($sql, [$leadId]);
    }

    public function createNota(array $datos): bool
    {
        $sql = "INSERT INTO notas_lead (
                    lead_id,
                    usuario_id,
                    tipo_actividad,
                    contenido
                ) VALUES (?, ?, ?, ?)";

        $resultado = $this->db->executeUpdate($sql, [
            $datos['lead_id'],
            $datos['usuario_id'],
            $datos['tipo_actividad'],
            $datos['contenido']
        ]);

        return $resultado !== false;
    }

    public function createHistorial(array $datos): bool
    {
        $sql = "INSERT INTO historial_lead (
                    lead_id,
                    usuario_id,
                    tipo_evento,
                    titulo,
                    descripcion,
                    estado_anterior,
                    estado_nuevo
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $resultado = $this->db->executeUpdate($sql, [
            $datos['lead_id'],
            $datos['usuario_id'],
            $datos['tipo_evento'],
            $datos['titulo'],
            $datos['descripcion'],
            $datos['estado_anterior'],
            $datos['estado_nuevo']
        ]);

        return $resultado !== false;
    }

    public function getDiasEnPanel(string $createdAt): int
    {
        if ($createdAt === '') {
            return 0;
        }

        $fechaCreacion = new \DateTime($createdAt);
        $hoy = new \DateTime();

        return (int)$fechaCreacion->diff($hoy)->days;
    }
}