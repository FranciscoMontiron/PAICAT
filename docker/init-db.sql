-- Script de inicialización para MariaDB
-- Crea la base de datos alumnos_utn y otorga permisos

-- Crear base de datos alumnos_utn
CREATE DATABASE IF NOT EXISTS `alumnos_utn` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Otorgar permisos al usuario paicat sobre ambas bases de datos
GRANT ALL PRIVILEGES ON `paicat`.* TO 'paicat'@'%';
GRANT ALL PRIVILEGES ON `alumnos_utn`.* TO 'paicat'@'%';
FLUSH PRIVILEGES;
