<?php

declare(strict_types=1);

namespace Sergio\App\models;

use DateTime;
use Sergio\Lib\Database;

class LeadModel
{
    private Database $db;
    private int $ultimoLeadId = 0;

    /** @var array<int, string> */
    private array $estados = [
        'Nuevo Lead',
        'Contactado',
        'En Progreso',
        'Objeciones',
        'Ganado',
        'Perdido'
    ];

    /** @var array<int, string> */
    private array $servicios = [
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

    /** @var array<int, string> */
    private array $prioridades = [
        'Baja',
        'Media',
        'Alta'
    ];

    public function __construct()
    {
        $this->db = new Database();
        $this->ensureSupportTables();
    }

    private function ensureSupportTables(): void
    {
        $this->db->executeUpdate("CREATE TABLE IF NOT EXISTS objetivos_mensuales (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            anio SMALLINT UNSIGNED NOT NULL,
            mes TINYINT UNSIGNED NOT NULL,
            objetivo_leads INT UNSIGNED NOT NULL DEFAULT 0,
            usuario_id INT UNSIGNED NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_objetivo_mes (anio, mes),
            CONSTRAINT fk_objetivos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->executeUpdate("CREATE TABLE IF NOT EXISTS notificaciones_app (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT UNSIGNED NOT NULL,
            usuario_origen_id INT UNSIGNED NULL,
            lead_id INT UNSIGNED NULL,
            tipo VARCHAR(50) NOT NULL,
            titulo VARCHAR(150) NOT NULL,
            mensaje TEXT NOT NULL,
            enlace VARCHAR(255) NULL,
            leida TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_notificaciones_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_notificaciones_origen FOREIGN KEY (usuario_origen_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
            CONSTRAINT fk_notificaciones_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function getObjetivoMesActual(int $usuarioId = 0, bool $esAdmin = true): array
    {
        $fechaActual = new DateTime('first day of this month');
        $fechaAnterior = new DateTime('first day of last month');

        $anioActual = (int) $fechaActual->format('Y');
        $mesActual = (int) $fechaActual->format('n');
        $anioAnterior = (int) $fechaAnterior->format('Y');
        $mesAnterior = (int) $fechaAnterior->format('n');

        $ganadosActual = $this->getGanadosScorePorMes($anioActual, $mesActual, $usuarioId, $esAdmin);
        $ganadosAnteriorReal = $this->getGanadosScorePorMes($anioAnterior, $mesAnterior, $usuarioId, $esAdmin);
        $ganadosAnteriorVisual = $ganadosAnteriorReal === 0 ? 20 : $ganadosAnteriorReal;

        $restantes = max(0, $ganadosAnteriorVisual - $ganadosActual);
        $porcentaje = $ganadosAnteriorVisual > 0
            ? (float) round(($ganadosActual / $ganadosAnteriorVisual) * 100, 1)
            : ($ganadosActual > 0 ? 100.0 : 0.0);

        return [
            'anio' => $anioActual,
            'mes' => $mesActual,
            'objetivo' => $ganadosAnteriorVisual,
            'ganados' => $ganadosActual,
            'restantes' => $restantes,
            'porcentaje' => $porcentaje,
            'ganados_mes_anterior' => $ganadosAnteriorVisual
        ];
    }

    private function getGanadosScorePorMes(int $anio, int $mes, int $usuarioId = 0, bool $esAdmin = true): int
    {
        $sql = "SELECT COUNT(DISTINCT l.id) AS total
                FROM leads l
                WHERE l.estado = 'Ganado'
                  AND l.lead_score = 1
                  AND EXISTS (
                      SELECT 1
                      FROM historial_lead h
                      WHERE h.lead_id = l.id
                        AND h.estado_nuevo = 'Ganado'
                        AND YEAR(h.created_at) = ?
                        AND MONTH(h.created_at) = ?
                  )";

        $params = [$anio, $mes];

        if (!$esAdmin && $usuarioId > 0) {
            $sql .= " AND l.responsable_id = ?";
            $params[] = $usuarioId;
        }

        $resultado = $this->db->executeQuery($sql, $params);
        return (int) ($resultado[0]['total'] ?? 0);
    }

    public function saveObjetivoMesActual(int $objetivoLeads, int $usuarioId): bool
    {
        $anio = (int) date('Y');
        $mes = (int) date('n');
        $objetivoLeads = max(0, $objetivoLeads);

        $sql = "INSERT INTO objetivos_mensuales (anio, mes, objetivo_leads, usuario_id)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    objetivo_leads = VALUES(objetivo_leads),
                    usuario_id = VALUES(usuario_id),
                    updated_at = NOW()";

        $resultado = $this->db->executeUpdate($sql, [$anio, $mes, $objetivoLeads, $usuarioId]);
        return $resultado !== false;
    }

    public function createGanadoNotificationForAll(int $leadId, int $usuarioOrigenId): bool
    {
        $leadInfo = $this->db->executeQuery(
            "SELECT l.id, l.servicios, l.responsable_id, u.nombre AS responsable_nombre
             FROM leads l
             LEFT JOIN usuarios u ON u.id = l.responsable_id
             WHERE l.id = ?
             LIMIT 1",
            [$leadId]
        );

        $lead = $leadInfo[0] ?? null;
        if (!$lead) {
            return false;
        }

        $resumenObjetivo = $this->getObjetivoMesActual();
        $responsableId = (int) ($lead['responsable_id'] ?? 0);
        $vendedor = (string) ($lead['responsable_nombre'] ?? 'El equipo comercial');
        $servicio = (string) ($lead['servicios'] ?? 'un nuevo curso');
        $ganadosMes = (int) ($resumenObjetivo['ganados'] ?? 0);
        $referenciaAnterior = (int) ($resumenObjetivo['ganados_mes_anterior'] ?? 0);
        $porcentaje = (float) ($resumenObjetivo['porcentaje'] ?? 0);
        $porcentajeTexto = rtrim(rtrim(number_format($porcentaje, 1, '.', ''), '0'), '.');

        $titulo = 'FELICIDADES';
        $mensaje = $vendedor . ' ha ganado un nuevo negocio en ' . $servicio . '. '
            . 'Objetivos del mes: ' . $ganadosMes . ' / ' . $referenciaAnterior . ' (' . $porcentajeTexto . '%).';

        $usuarios = $this->db->executeQuery(
            "SELECT id FROM usuarios WHERE activo = 1 ORDER BY id ASC"
        );

        foreach ($usuarios as $usuario) {
            $destinoId = (int) ($usuario['id'] ?? 0);
            if ($destinoId <= 0) {
                continue;
            }

            $this->db->executeUpdate(
                "INSERT INTO notificaciones_app (usuario_id, usuario_origen_id, lead_id, tipo, titulo, mensaje, enlace, leida)
                 VALUES (?, ?, ?, 'logro_ganado', ?, ?, ?, 0)",
                [$destinoId, $responsableId > 0 ? $responsableId : ($usuarioOrigenId > 0 ? $usuarioOrigenId : null), $leadId, $titulo, $mensaje, 'leads/' . $leadId]
            );
        }

        return true;
    }

    public function getPendingGanadoNotificationsByUsuario(int $usuarioId): array
    {
        $sql = "SELECT n.*, u.nombre AS origen_nombre
                FROM notificaciones_app n
                LEFT JOIN usuarios u ON u.id = n.usuario_origen_id
                WHERE n.usuario_id = ?
                  AND n.tipo = 'logro_ganado'
                  AND n.leida = 0
                ORDER BY n.created_at ASC, n.id ASC";

        return $this->db->executeQuery($sql, [$usuarioId]);
    }

    public function markNotificationsAsReadByIds(array $notificacionesIds): void
    {
        $idsValidos = array_values(array_filter(array_map('intval', $notificacionesIds), static fn (int $id): bool => $id > 0));
        if (empty($idsValidos)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($idsValidos), '?'));
        $this->db->executeUpdate(
            "UPDATE notificaciones_app SET leida = 1 WHERE id IN ($placeholders)",
            $idsValidos
        );
    }

    public function getEstados(): array
    {
        return $this->estados;
    }

    public function getServicios(): array
    {
        return $this->servicios;
    }

    public function getPrioridades(): array
    {
        return $this->prioridades;
    }

    public function getResponsables(): array
    {
        $sql = "SELECT id, nombre, rol
                FROM usuarios
                WHERE activo = 1
                ORDER BY nombre ASC";

        return $this->db->executeQuery($sql, []);
    }

    public function getUltimoLeadId(): int
    {
        return $this->ultimoLeadId;
    }

    public function create(array $datos): bool
    {
        $this->ultimoLeadId = 0;

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
            $datos['estado'] ?? ESTADO_POR_DEFECTO,
            $datos['responsable_id'] ?? null,
            $datos['servicios'],
            $datos['indicaciones'] ?? null,
            ($datos['estado'] ?? ESTADO_POR_DEFECTO) === 'Ganado' ? 1 : ((($datos['estado'] ?? ESTADO_POR_DEFECTO) === 'Perdido') ? 0 : ($datos['lead_score'] ?? 0)),
            $datos['email'] ?? null,
            $datos['telefono'] ?? null,
            $datos['valor'] ?? null,
            $datos['ultimo_contacto'] ?? null,
            $datos['prioridad'] ?? PRIORIDAD_POR_DEFECTO,
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
                    l.responsable_id,
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

        /** @var array<string, array<int, array<string, mixed>>> $agrupados */
        $agrupados = [];

        foreach ($this->estados as $nombreEstado) {
            $agrupados[(string) $nombreEstado] = [];
        }

        foreach ($resultados as $lead) {
            $estadoLead = (string) ($lead['estado'] ?? '');
            if (!isset($agrupados[$estadoLead])) {
                $agrupados[$estadoLead] = [];
            }
            $agrupados[$estadoLead][] = $lead;
        }

        return $agrupados;
    }

    public function updateEstado(int $id, string $estado): bool
    {
        $sql = "UPDATE leads
                SET estado = ?,
                    lead_score = CASE
                        WHEN ? = 'Ganado' THEN 1
                        WHEN ? = 'Perdido' THEN 0
                        ELSE lead_score
                    END,
                    updated_at = NOW()
                WHERE id = ?";

        $resultado = $this->db->executeUpdate($sql, [$estado, $estado, $estado, $id]);

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
                    n.lead_id,
                    n.usuario_id,
                    n.tipo_actividad,
                    n.contenido,
                    n.created_at,
                    u.nombre AS usuario_nombre
                FROM notas_lead n
                INNER JOIN usuarios u ON n.usuario_id = u.id
                WHERE n.lead_id = ?
                ORDER BY n.created_at DESC, n.id DESC";

        return $this->db->executeQuery($sql, [$leadId]);
    }

    public function getHistorialByLead(int $leadId): array
    {
        $sql = "SELECT
                    h.id,
                    h.lead_id,
                    h.usuario_id,
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
            $datos['usuario_id'] ?? null,
            $datos['tipo_evento'],
            $datos['titulo'],
            $datos['descripcion'] ?? null,
            $datos['estado_anterior'] ?? null,
            $datos['estado_nuevo'] ?? null
        ]);

        return $resultado !== false;
    }

    public function updateUltimoContacto(int $leadId, ?string $fecha = null): bool
    {
        $fechaContacto = $fecha ?? date('Y-m-d H:i:s');

        $sql = "UPDATE leads
                SET ultimo_contacto = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $resultado = $this->db->executeUpdate($sql, [$fechaContacto, $leadId]);

        return $resultado !== false;
    }

    public function getDiasEnPanel(string $fechaCreacion): int
    {
        if ($fechaCreacion === '') {
            return 0;
        }

        $fechaInicio = new DateTime($fechaCreacion);
        $fechaActual = new DateTime();

        return (int) $fechaInicio->diff($fechaActual)->days;
    }

    public function update(int $id, array $datos): bool
    {
        $sql = "UPDATE leads
                SET lead_nombre = ?,
                    estado = ?,
                    responsable_id = ?,
                    servicios = ?,
                    indicaciones = ?,
                    lead_score = ?,
                    email = ?,
                    telefono = ?,
                    valor = ?,
                    ultimo_contacto = ?,
                    prioridad = ?,
                    origen = ?,
                    created_at = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $resultado = $this->db->executeUpdate($sql, [
            $datos['lead_nombre'],
            $datos['estado'],
            $datos['responsable_id'],
            $datos['servicios'],
            $datos['indicaciones'],
            ($datos['estado'] ?? '') === 'Ganado' ? 1 : ((($datos['estado'] ?? '') === 'Perdido') ? 0 : $datos['lead_score']),
            $datos['email'],
            $datos['telefono'],
            $datos['valor'],
            $datos['ultimo_contacto'],
            $datos['prioridad'],
            $datos['origen'],
            $datos['created_at'],
            $id
        ]);

        return $resultado !== false;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM leads WHERE id = ?";
        $resultado = $this->db->executeUpdate($sql, [$id]);

        return $resultado !== false;
    }

    private function buildLeadDashboardWhere(array $filtros, int $usuarioId, bool $esAdmin, string $alias = 'l'): array
    {
        $condiciones = [];
        $params = [];

        if ($esAdmin) {
            $usuarioFiltro = (int) ($filtros['usuario_id'] ?? 0);
            if ($usuarioFiltro > 0) {
                $condiciones[] = $alias . ".responsable_id = ?";
                $params[] = $usuarioFiltro;
            }
        } else {
            $condiciones[] = $alias . ".responsable_id = ?";
            $params[] = $usuarioId;
        }

        if (!empty($filtros['fecha_desde'])) {
            $condiciones[] = "DATE(" . $alias . ".created_at) >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $condiciones[] = "DATE(" . $alias . ".created_at) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['servicios'])) {
            $condiciones[] = $alias . ".servicios = ?";
            $params[] = $filtros['servicios'];
        }

        if (!empty($filtros['estado'])) {
            $condiciones[] = $alias . ".estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['origen'])) {
            $condiciones[] = $alias . ".origen = ?";
            $params[] = $filtros['origen'];
        }

        $where = '';
        if (!empty($condiciones)) {
            $where = ' WHERE ' . implode(' AND ', $condiciones);
        }

        return [$where, $params];
    }

    public function getDashboardResumenGeneral(int $usuarioId, bool $esAdmin, array $filtros = []): array
    {
        [$where, $params] = $this->buildLeadDashboardWhere($filtros, $usuarioId, $esAdmin, 'l');

        $sql = "SELECT
                    COUNT(*) AS total_leads,
                    SUM(CASE WHEN l.estado = 'Ganado' THEN 1 ELSE 0 END) AS leads_ganados,
                    SUM(CASE WHEN l.estado = 'Perdido' THEN 1 ELSE 0 END) AS leads_perdidos,
                    SUM(CASE WHEN l.estado = 'Objeciones' THEN 1 ELSE 0 END) AS leads_objeciones,
                    COALESCE(SUM(CASE WHEN l.estado <> 'Perdido' THEN l.valor ELSE 0 END), 0) AS valor_pipeline,
                    COALESCE(SUM(CASE WHEN l.estado = 'Ganado' THEN l.valor ELSE 0 END), 0) AS valor_ganado,
                    COALESCE(SUM(CASE WHEN l.estado = 'Perdido' THEN l.valor ELSE 0 END), 0) AS valor_perdido
                FROM leads l
                $where";

        $resultado = $this->db->executeQuery($sql, $params);
        $fila = $resultado[0] ?? [];

        $total = (int) ($fila['total_leads'] ?? 0);
        $ganados = (int) ($fila['leads_ganados'] ?? 0);

        return [
            'total_leads'      => $total,
            'leads_ganados'    => $ganados,
            'leads_perdidos'   => (int) ($fila['leads_perdidos'] ?? 0),
            'leads_objeciones' => (int) ($fila['leads_objeciones'] ?? 0),
            'valor_pipeline'   => (float) ($fila['valor_pipeline'] ?? 0),
            'valor_ganado'     => (float) ($fila['valor_ganado'] ?? 0),
            'valor_perdido'    => (float) ($fila['valor_perdido'] ?? 0),
            'conversion'       => $total > 0 ? round(($ganados / $total) * 100, 1) : 0
        ];
    }

    public function getResumenPipeline(int $usuarioId, bool $esAdmin, array $filtros = []): array
    {
        [$where, $params] = $this->buildLeadDashboardWhere($filtros, $usuarioId, $esAdmin, 'l');

        $sql = "SELECT
                    l.estado,
                    COUNT(*) AS total,
                    COALESCE(SUM(l.valor), 0) AS valor_total,
                    ROUND(AVG(DATEDIFF(CURDATE(), DATE(l.created_at))), 1) AS media_dias
                FROM leads l
                $where
                GROUP BY l.estado";

        $filas = $this->db->executeQuery($sql, $params);

        /** @var array<string, array<string, int|float|string>> $resumen */
        $resumen = [];

        foreach ($this->estados as $nombreEstado) {
            $resumen[(string) $nombreEstado] = [
                'estado'      => (string) $nombreEstado,
                'total'       => 0,
                'valor_total' => 0.0,
                'media_dias'  => 0.0,
                'porcentaje'  => 0.0
            ];
        }

        $totalLeads = 0;

        foreach ($filas as $fila) {
            $estadoFila = (string) ($fila['estado'] ?? '');
            $totalFila = (int) ($fila['total'] ?? 0);

            if (!isset($resumen[$estadoFila])) {
                $resumen[$estadoFila] = [
                    'estado'      => $estadoFila,
                    'total'       => 0,
                    'valor_total' => 0.0,
                    'media_dias'  => 0.0,
                    'porcentaje'  => 0.0
                ];
            }

            $resumen[$estadoFila] = [
                'estado'      => $estadoFila,
                'total'       => $totalFila,
                'valor_total' => (float) ($fila['valor_total'] ?? 0),
                'media_dias'  => (float) ($fila['media_dias'] ?? 0),
                'porcentaje'  => 0.0
            ];

            $totalLeads += $totalFila;
        }

        if ($totalLeads > 0) {
            foreach ($resumen as $nombreEstado => $datosEstado) {
                $resumen[$nombreEstado]['porcentaje'] = round((((int) $datosEstado['total']) / $totalLeads) * 100, 1);
            }
        }

        return $resumen;
    }

    public function getLeadsSinContacto(int $usuarioId, bool $esAdmin, array $filtros = [], int $limite = 6): array
    {
        [$whereBase, $params] = $this->buildLeadDashboardWhere($filtros, $usuarioId, $esAdmin, 'l');

        $condicionContacto = "l.ultimo_contacto IS NULL";

        if ($whereBase === '') {
            $where = ' WHERE ' . $condicionContacto;
        } else {
            $where = $whereBase . ' AND ' . $condicionContacto;
        }

        $sql = "SELECT
                    l.id,
                    l.lead_nombre,
                    l.estado,
                    l.servicios,
                    l.ultimo_contacto,
                    l.valor,
                    u.nombre AS responsable_nombre
                FROM leads l
                LEFT JOIN usuarios u ON l.responsable_id = u.id
                $where
                ORDER BY COALESCE(l.ultimo_contacto, l.created_at) ASC
                LIMIT " . (int) $limite;

        return $this->db->executeQuery($sql, $params);
    }

    public function getResumenPorUsuario(array $filtros = []): array
    {
        $join = " LEFT JOIN leads l ON l.responsable_id = u.id";
        $params = [];

        if (!empty($filtros['fecha_desde'])) {
            $join .= " AND DATE(l.created_at) >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $join .= " AND DATE(l.created_at) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['servicios'])) {
            $join .= " AND l.servicios = ?";
            $params[] = $filtros['servicios'];
        }

        if (!empty($filtros['estado'])) {
            $join .= " AND l.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['origen'])) {
            $join .= " AND l.origen = ?";
            $params[] = $filtros['origen'];
        }

        $sql = "SELECT
                    u.id,
                    u.nombre,
                    u.rol,
                    COUNT(l.id) AS total_leads,
                    SUM(CASE WHEN l.estado = 'Ganado' THEN 1 ELSE 0 END) AS ganados,
                    COALESCE(SUM(CASE WHEN l.estado = 'Ganado' THEN l.valor ELSE 0 END), 0) AS valor_ganado
                FROM usuarios u
                $join
                WHERE u.activo = 1
                GROUP BY u.id, u.nombre, u.rol
                ORDER BY valor_ganado DESC, total_leads DESC, u.nombre ASC";

        $filas = $this->db->executeQuery($sql, $params);

        foreach ($filas as &$fila) {
            $total = (int) ($fila['total_leads'] ?? 0);
            $ganados = (int) ($fila['ganados'] ?? 0);
            $fila['conversion'] = $total > 0 ? round(($ganados / $total) * 100, 1) : 0;
        }

        return $filas;
    }

    public function getListadoFiltrado(int $usuarioId, bool $esAdmin, array $filtros = []): array
    {
        [$where, $params] = $this->buildLeadDashboardWhere($filtros, $usuarioId, $esAdmin, 'l');

        $sql = $this->getListadoSelectSql() . "
                $where
                ORDER BY l.created_at DESC, l.id DESC";

        return $this->db->executeQuery($sql, $params);
    }

    public function getListado(): array
    {
        return $this->getListadoFiltrado(0, true, []);
    }

    public function getListadoVendedor(int $id): array
    {
        return $this->getListadoFiltrado(0, true, [
            'usuario_id' => $id
        ]);
    }

    public function getListadoEstado(string $estado): array
    {
        return $this->getListadoFiltrado(0, true, [
            'estado' => $estado
        ]);
    }

    public function getListadoFecha(string $inicio, string $fin): array
    {
        return $this->getListadoFiltrado(0, true, [
            'fecha_desde' => substr($inicio, 0, 10),
            'fecha_hasta' => substr($fin, 0, 10)
        ]);
    }

    private function getListadoSelectSql(): string
    {
        return "SELECT
                    l.id,
                    l.lead_nombre,
                    l.estado,
                    l.responsable_id,
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
                LEFT JOIN usuarios u ON l.responsable_id = u.id";
    }
}