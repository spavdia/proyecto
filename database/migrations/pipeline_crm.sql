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
    'Vendedor',
    'ventas@crm.com',
    '$2y$10$Fw9y0ZXg2N2D6NoidCjFteUYm9P0Sc6BnPrWhDRbsB7/eRzua/GDK',
    'ventas',
    1
);
