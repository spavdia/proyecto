USE pipeline_crm;

CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead VARCHAR(150) NOT NULL,
    estado ENUM('Nuevo Lead', 'Contactado', 'En Progreso', 'Objeciones', 'Ganado', 'Perdido')
        NOT NULL DEFAULT 'Nuevo Lead',
    responsable_id INT UNSIGNED NULL,
    curso ENUM(
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
    ) NOT NULL,
    indicaciones TEXT NULL,
    lead_score INT UNSIGNED NOT NULL DEFAULT 0,
    email VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,
    valor DECIMAL(10,2) NULL,
    ultimo_contacto DATETIME NULL,
    prioridad ENUM('Baja', 'Media', 'Alta') NOT NULL DEFAULT 'Media',
    origen ENUM('formulario_web', 'app_interna') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_leads_responsable
        FOREIGN KEY (responsable_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;