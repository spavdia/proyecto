DROP DATABASE IF EXISTS pipeline_crm;

CREATE DATABASE IF NOT EXISTS pipeline_crm
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE pipeline_crm;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'ventas') NOT NULL DEFAULT 'ventas',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (nombre, email, password_hash, rol, activo)
VALUES
(
    'Administrador',
    'admin@crm.com',
    '$2y$10$Fw9y0ZXg2N2D6NoidCjFteUYm9P0Sc6BnPrWhDRbsB7/eRzua/GDK',
    'admin',
    1
),
(
    'Ana',
    'ana@crm.com',
    '$2y$10$Fw9y0ZXg2N2D6NoidCjFteUYm9P0Sc6BnPrWhDRbsB7/eRzua/GDK',
    'ventas',
    1
),
(
    'Juan',
    'juan@crm.com',
    '$2y$10$Fw9y0ZXg2N2D6NoidCjFteUYm9P0Sc6BnPrWhDRbsB7/eRzua/GDK',
    'ventas',
    1
);

CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_nombre VARCHAR(150) NOT NULL,
    estado ENUM('Nuevo Lead', 'Contactado', 'En Progreso', 'Objeciones', 'Ganado', 'Perdido')
        NOT NULL DEFAULT 'Nuevo Lead',
    responsable_id INT UNSIGNED NULL,
    servicios ENUM(
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

CREATE TABLE IF NOT EXISTS notas_lead (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    tipo_actividad ENUM('Llamada', 'Email', 'Cita presencial') NOT NULL,
    contenido TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notas_lead_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_notas_lead_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
