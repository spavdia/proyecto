DROP DATABASE IF EXISTS pipeline_crm;

CREATE DATABASE IF NOT EXISTS pipeline_crm
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

# CREAMOS TABLA USUARIOS

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


# INSERTAMOS USUARIOS

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

# INSERTAMOS TABLA LEADS
CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_nombre VARCHAR(150) NOT NULL,
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
