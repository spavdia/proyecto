CREATE TABLE IF NOT EXISTS tareas_lead (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    usuario_creador_id INT UNSIGNED NOT NULL,
    usuario_asignado_id INT UNSIGNED NOT NULL,
    tipo_actividad ENUM('Llamada', 'Email', 'Cita presencial') NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_final DATETIME NOT NULL,
    estado ENUM('Pendiente', 'En curso', 'Terminada') NOT NULL DEFAULT 'Pendiente',
    leida_asignado TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_tareas_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_tareas_creador
        FOREIGN KEY (usuario_creador_id) REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_tareas_asignado
        FOREIGN KEY (usuario_asignado_id) REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;