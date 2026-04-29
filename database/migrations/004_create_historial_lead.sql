USE pipeline_crm;

CREATE TABLE IF NOT EXISTS historial_lead (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NULL,
    tipo_evento ENUM('alta', 'nota', 'cambio_estado') NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    estado_anterior VARCHAR(50) NULL,
    estado_nuevo VARCHAR(50) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historial_lead_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_historial_lead_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
