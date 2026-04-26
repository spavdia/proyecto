<?php
/*
create(array $datos)
getByEstado(string $estado)
getAgrupadosPorEstado()
findById(int $id) más adelante
update() más adelante*/

namespace Sergio\App\models;
use Sergio\Lib\Database;

class LeadModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    private array $estados = [
        'Nuevo Lead',
        'Contactado',
        'En Progreso',
        'Objeciones',
        'Ganado',
        'Perdido'
    ];

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

    public function getPrioridades(): array
    {
        return ['Baja', 'Media', 'Alta'];
    }

    //crea 
    public function create(array $datos): bool
{
    //definimos valor por defecto
    $estado = $datos['estado'] ?? 'Nuevo Lead';
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

    return $resultado !== false;
}

    // devuelve array asociativo por estado
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
                    u.nombre AS responsable_nombre
                FROM leads l
                LEFT JOIN usuarios u ON l.responsable_id = u.id
                ORDER BY l.created_at DESC";

        $resultados = $this->db->executeQuery($sql);

        $agrupados = [];

        foreach ($this->estados as $estado) {
            $agrupados[$estado] = [];
        }

        foreach ($resultados as $lead) {
            $agrupados[$lead['estado']][] = $lead;
        }

        return $agrupados;
    }


}


?>