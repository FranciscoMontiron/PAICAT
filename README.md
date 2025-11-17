#  PAICAT - Plataforma de Administración del Ingreso UTN FRLP

Sistema de gestión integral para el Curso de Ingreso de la Universidad Tecnológica Nacional - Facultad Regional La Plata.

##  Descripción

PAICAT es una plataforma web desarrollada en Laravel 11 que permite gestionar de manera eficiente todo el proceso del curso de ingreso universitario, incluyendo inscripciones, asistencias, calificaciones y generación de reportes.

### Características Principales

-  **Autenticación y autorización** basada en roles (Admin, Coordinador, Docente, Alumno)
-  **Gestión de comisiones** con control de cupos
-  **Administración de alumnos** y docentes
-  **Registro de asistencias** con validación de porcentajes mínimos
-  **Carga de calificaciones** por materia (Física, Matemática, Química)
-  **Generación de reportes** en PDF y Excel
-  **Sistema de recuperatorios** configurable
-  **Notificaciones por email** vía Mailhog (desarrollo)
-  **Interfaz moderna** con Tailwind CSS y Alpine.js

##  Requisitos Previos

- Docker 20.10+
- Docker Compose 2.0+
- Git

##  Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/paicat.git
cd paicat
```

### 2. Copiar archivo de configuración

```bash
cp .env.example .env
```

### 3. Levantar los contenedores Docker

```bash
docker-compose up -d
```

El script de entrypoint se encargará automáticamente de:
-  Esperar a que MariaDB esté disponible
-  Crear la base de datos
-  Instalar dependencias de Composer y NPM
-  Generar la clave de la aplicación
-  Ejecutar las migraciones
-  Ejecutar los seeders (creando el usuario admin)
-  Configurar permisos

### 4. Acceder a la aplicación

- **Aplicación**: http://localhost
- **Vite Dev Server (HMR)**: http://localhost:5173 *(se levanta automáticamente)*
- **PHPMyAdmin**: http://localhost:8081
- **Mailhog** (visor de emails): http://localhost:8025

> **Nota:** El servidor de Vite se levanta automáticamente en un contenedor separado, proporcionando Hot Module Replacement (HMR) para desarrollo. No necesitas ejecutar `npm run dev` manualmente.

## 🔑 Credenciales de Acceso

### Usuario Administrador
- **Email**: admin@paicat.utn.edu.ar
- **Password**: admin123

### Base de Datos (PHPMyAdmin)
- **Servidor**: mariadb
- **Usuario**: root
- **Password**: root

## 🛠️ Desarrollo

### Hot Module Replacement (HMR) Automático

El proyecto incluye un contenedor dedicado para Vite que se levanta automáticamente con `docker-compose up -d`. Esto significa que:

-  **HMR siempre activo**: Los cambios en CSS/JS se reflejan instantáneamente
-  **No requiere comandos manuales**: Se levanta automáticamente
-  **Logs independientes**: `docker-compose logs -f vite`
-  **Reinicio simple**: `docker-compose restart vite`

### Detener Vite (Opcional)

Si no necesitas HMR (por ejemplo, trabajando solo en backend):

```bash
docker-compose stop vite
```

Para volver a levantarlo:

```bash
docker-compose start vite
```

### Compilar assets para producción

```bash
docker exec -it paicat_vite npm run build
```

### Ejecutar tests

```bash
docker exec -it paicat_php php artisan test
```

### Acceder al contenedor PHP

```bash
docker exec -it paicat_php bash
```

### Ver logs

```bash
# Logs de todos los servicios
docker-compose logs -f

# Logs de servicios específicos
docker-compose logs -f php
docker-compose logs -f vite
docker-compose logs -f nginx
docker-compose logs -f mariadb
```

##  Base de Datos

### Conexiones configuradas

El sistema está preparado para trabajar con tres bases de datos:

1. **paicat** (principal - lectura/escritura): Datos del sistema actual
2. **alumnos_utn** (solo lectura): Sistema anterior de alumnos
3. **sysacad** (solo lectura): Datos maestros del sistema académico

### Ejecutar migraciones manualmente

```bash
docker exec -it paicat_php php artisan migrate
```

### Ejecutar seeders manualmente

```bash
docker exec -it paicat_php php artisan db:seed
```

### Resetear base de datos

```bash
docker exec -it paicat_php php artisan migrate:fresh --seed
```

## 🔧 Comandos Útiles

### Limpiar caché

```bash
docker exec -it paicat_php php artisan optimize:clear
```

### Generar nueva clave de aplicación

```bash
docker exec -it paicat_php php artisan key:generate
```

### Crear un nuevo controlador

```bash
docker exec -it paicat_php php artisan make:controller NombreController
```

### Crear un nuevo modelo con migración

```bash
docker exec -it paicat_php php artisan make:model NombreModelo -m
```

##  Roles y Permisos

El sistema implementa 4 roles principales:

1. **Admin**: Acceso completo al sistema
2. **Coordinador**: Gestión de comisiones, docentes y reportes
3. **Docente**: Carga de asistencias y calificaciones
4. **Alumno**: Visualización de datos personales y calificaciones

##  Configuración Avanzada

### Variables de entorno importantes

```env
# Configuración de cupos y aprobación
PAICAT_CUPO_MAXIMO_COMISION=40
PAICAT_NOTA_APROBACION=6.00
PAICAT_PORCENTAJE_ASISTENCIA_MINIMO=75
PAICAT_HABILITAR_RECUPERATORIOS=true
```



