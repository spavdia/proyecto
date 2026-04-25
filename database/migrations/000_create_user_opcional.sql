

use pipeline_crm;

# creo super usuario, por si tengo que entrar sin root
-- Crear usuario
CREATE USER 'crm_admin'@'localhost'
IDENTIFIED BY '';

-- Dar todos los permisos sobre la base de datos
GRANT ALL PRIVILEGES ON pipeline_crm.* 
TO 'crm_admin'@'localhost';

-- Aplicar cambios
FLUSH PRIVILEGES;