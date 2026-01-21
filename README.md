# PAICAT

Plataforma de Administración del Ingreso - UTN FRLP

Sistema de gestión para el Curso de Ingreso de la Universidad Tecnológica Nacional - Facultad Regional La Plata.

## Requisitos

- Docker 20.10+
- Docker Compose 2.0+

## Instalación

### 1. Clonar repositorio

```bash
git clone https://github.com/tu-usuario/paicat.git
cd paicat
```

### 2. Descargar archivo de alumnos

Descargar el archivo `alumnos.sql` desde el Drive del proyecto y colocarlo en:

```
bases_externas/alumnos.sql
```

### 3. Configurar e iniciar

```bash
# Copiar configuración
cp .env.example .env

# Levantar contenedores
docker compose up -d --build

# Instalar dependencias
docker compose exec app composer install

# Configurar base de datos (migraciones + seeders)
docker compose exec app php artisan paicat:setup --fresh --seed

# Importar alumnos desde archivo SQL
docker compose exec app php artisan paicat:import-alumnos
```

### 4. Acceder

Abrir en el navegador: **http://localhost**

## Credenciales

### Aplicación

| Campo    | Valor                     |
| -------- | ------------------------- |
| Email    | `admin@paicat.utn.edu.ar` |
| Password | `admin123`                |

### Base de Datos

| Campo    | Valor       |
| -------- | ----------- |
| Host     | `localhost` |
| Puerto   | `3307`      |
| Database | `paicat`    |
| Usuario  | `paicat`    |
| Password | `paicat`    |

## Comandos Útiles

```bash
# Acceder al contenedor
docker compose exec app bash

# Resetear base de datos completamente
docker compose exec app php artisan paicat:setup --fresh --seed

# Re-importar alumnos
docker compose exec app php artisan paicat:import-alumnos

# Ver logs
docker compose logs -f app

# Detener contenedores
docker compose down
```

## Estructura de Contenedores

| Servicio    | Puerto | Descripción            |
| ----------- | ------ | ---------------------- |
| **app**     | 80     | Laravel + Apache       |
| **mariadb** | 3307   | MariaDB 11             |
| **vite**    | 5173   | Hot Module Replacement |

## Licencia

TODO
