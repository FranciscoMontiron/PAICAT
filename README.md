# PAICAT

Plataforma de Administración del Ingreso - UTN FRLP

Sistema de gestión para el Curso de Ingreso de la Universidad Tecnológica Nacional - Facultad Regional La Plata. Desarrollado con Laravel 11 + Tailwind CSS.

## Requisitos

- Docker 20.10+
- Docker Compose 2.0+

## Instalación Rápida

```bash
# 1. Clonar repositorio
git clone https://github.com/tu-usuario/paicat.git
cd paicat

# 2. Copiar configuración
cp .env.example .env

# 3. Levantar contenedores
docker compose up -d

# 4. Configurar aplicación (migraciones + seeders)
docker compose exec app composer install
docker compose exec app php artisan paicat:setup --seed
```

Acceder en: **http://localhost**

## Credenciales

### Aplicación

| Campo    | Valor                     |
| -------- | ------------------------- |
| Email    | `admin@paicat.utn.edu.ar` |
| Password | `admin123`                |

### Base de Datos (Workbench/DBeaver/etc)

| Campo         | Valor           |
| ------------- | --------------- |
| Host          | `localhost`     |
| Puerto        | `3306`          |
| Base de datos | `paicat`        |
| Usuario       | `paicat`        |
| Password      | `paicat`        |
| Usuario root  | `root` / `root` |

## Estructura Docker

### Desarrollo (docker-compose.yml)

```bash
docker compose up -d
```

| Servicio    | Puerto | Descripción            |
| ----------- | ------ | ---------------------- |
| **app**     | 80     | Laravel + Apache       |
| **mariadb** | 3306   | Base de datos          |
| **vite**    | 5173   | Hot Module Replacement |

### Producción (docker-compose.prod.yml)

```bash
docker compose -f docker-compose.prod.yml up -d
```

Solo incluye `app` y `mariadb`. Configurar variables en `.env` antes de usar.

## Comandos Útiles

```bash
# Acceder al contenedor
docker compose exec app bash

# Ejecutar setup (migraciones + seeders + cache)
docker compose exec app php artisan paicat:setup --seed

# Resetear base de datos
docker compose exec app php artisan paicat:setup --fresh --seed

# Ver logs
docker compose logs -f app

# Compilar assets para producción
docker compose exec vite npm run build
```

## Desarrollo

El contenedor `vite` se levanta automáticamente con Hot Module Replacement. Los cambios en CSS/JS se reflejan instantáneamente sin necesidad de refrescar.

Para detener Vite (si solo trabajas en backend):

```bash
docker compose stop vite
```

## Roles

| Rol             | Acceso                         |
| --------------- | ------------------------------ |
| **Admin**       | Completo                       |
| **Coordinador** | Comisiones, docentes, reportes |
| **Docente**     | Asistencias, calificaciones    |
| **Alumno**      | Datos personales               |

## Licencia

MIT
