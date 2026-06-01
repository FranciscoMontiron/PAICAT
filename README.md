# PAICAT

Plataforma de Administración del Ingreso - UTN FRLP

Sistema de gestión para el Curso de Ingreso de la Universidad Tecnológica Nacional - Facultad Regional La Plata.

## Requisitos

- Docker 20.10+
- Docker Compose 2.0+
- Git

## Instalación desde cero

### 1. Clonar repositorio

```bash
git clone https://github.com/FranciscoMontiron/PAICAT.git
cd PAICAT
```

> El repositorio ya incluye `database/data/Datos Sysacad.xlsx` (datos maestros de países, provincias y escuelas). No es necesario descargarlo por separado.

### 2. Descargar archivo de alumnos

Descargar el archivo `alumnos.sql` desde el Drive del proyecto y colocarlo en:

```
bases_externas/alumnos.sql
```

> Sin este archivo el sistema funciona, pero no tendrá datos de alumnos para inscribir.

### 3. Configurar el entorno

```bash
cp .env.example .env
```

### 4. Levantar contenedores

```bash
docker compose up -d --build
```

Esperar que MariaDB termine de inicializarse (~1-2 minutos). Podés verificar con:

```bash
docker compose logs mariadb
# Cuando aparezca "mariadb ready for connections" podés continuar
```

### 5. Instalar dependencias PHP

```bash
docker compose exec app composer install
```

### 6. Configurar base de datos

```bash
docker compose exec app php artisan paicat:setup --fresh --seed
```

Este comando hace todo automáticamente:
- Genera la `APP_KEY`
- Limpia caché
- Crea todas las tablas (`migrate:fresh`)
- Carga datos iniciales: roles, permisos, usuario admin, configuración del sistema y datos de Sysacad
- Crea el enlace de storage
- Optimiza la aplicación

### 7. Importar datos de alumnos

```bash
docker compose exec app php artisan paicat:import-alumnos
```

> Requiere `bases_externas/alumnos.sql` del paso 2. Si no tenés el archivo, podés saltar este paso.

### 8. Acceder

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

---

##  Configuración

### Conexión a la base de datos de alumnos (`alumnos_utn`)

> [!IMPORTANT]
> La base de datos `alumnos_utn` es una BD externa de solo lectura que contiene los datos de los aspirantes (nombres, DNI, formulario de preinscripción, etc.). **Sin ella el sistema funciona**, pero no mostrará datos de alumnos en las inscripciones y el buscador de aspirantes estará deshabilitado.

El sistema detecta automáticamente si la conexión está disponible y muestra un aviso descriptivo en pantalla en lugar de un error fatal cuando no lo está.

#### Variables de entorno relevantes (`.env`)

| Variable | Por defecto | Descripción |
|---|---|---|
| `DB_HOST_ALUMNOS` | hereda `DB_HOST` | Host del servidor de BD |
| `DB_PORT_ALUMNOS` | hereda `DB_PORT` | Puerto del servidor |
| `DB_DATABASE_ALUMNOS` | `alumnos_utn` | Nombre de la base de datos |
| `DB_USERNAME_ALUMNOS` | hereda `DB_USERNAME` | Usuario con acceso a la BD |
| `DB_PASSWORD_ALUMNOS` | hereda `DB_PASSWORD` | Contraseña del usuario |

#### Escenarios de configuración

**A) BD en el mismo servidor que PAICAT (configuración por defecto)**

Si `alumnos_utn` está en el mismo MariaDB que la BD principal, no hace falta configurar nada extra. Solo asegurate de que `DB_DATABASE_ALUMNOS` esté definido en `.env`:

```env
DB_DATABASE_ALUMNOS=alumnos_utn
```

Las demás variables (`DB_HOST_ALUMNOS`, `DB_PORT_ALUMNOS`, etc.) se heredan automáticamente de la conexión principal.

**B) BD en un servidor externo (integración con sistema de la facultad)**

Configurá todas las variables en `.env` apuntando al servidor remoto:

```env
DB_HOST_ALUMNOS=192.168.1.100
DB_PORT_ALUMNOS=3306
DB_DATABASE_ALUMNOS=alumnos_utn
DB_USERNAME_ALUMNOS=usuario_externo
DB_PASSWORD_ALUMNOS=contraseña_externa
```

**C) Sin BD de alumnos (modo reducido)**

Si no se configuran las variables o la BD no está disponible, el sistema opera con funcionalidad reducida:
- Las páginas de inscripciones muestran un aviso amarillo con instrucciones.
- El buscador de aspirantes devuelve un mensaje de error en lugar de resultados.
- Las demás funciones (comisiones, notas, asistencia) no se ven afectadas.

Para cargar datos de prueba una vez que la BD esté disponible:

```bash
docker compose exec app php artisan paicat:import-alumnos
```

---

## Datos cargados automáticamente por el seeder

| Dato | Fuente |
|------|--------|
| Roles y permisos del sistema | `RolesAndPermissionsSeeder` |
| Usuario admin | `CreateAdminUserSeeder` |
| Variables de configuración del sistema | `ConfiguracionSeeder` |
| Países, provincias, escuelas (Sysacad) | `SysacadDataSeeder` + `database/data/Datos Sysacad.xlsx` |

## Datos NO recuperables sin backup de producción

Los siguientes datos no tienen seeder y se pierden al hacer `migrate:fresh`:

- Municipios, aulas y comisiones
- Inscripciones y cursadas
- Docentes y alumnos cargados manualmente

## Comandos Útiles

```bash
# Acceder al contenedor de la app
docker compose exec app bash

# Limpiar caché de config, rutas y vistas
docker compose exec app php artisan optimize:clear

# Resetear base de datos completamente (borra todo y re-seedea)
docker compose exec app php artisan paicat:setup --fresh --seed

# Re-importar alumnos
docker compose exec app php artisan paicat:import-alumnos

# Importar alumnos desde otro archivo
docker compose exec app php artisan paicat:import-alumnos --file=bases_externas/otro_archivo.sql

# Ver logs de la app
docker compose logs -f app

# Detener contenedores
docker compose down

# Detener y borrar volúmenes (borra la base de datos)
docker compose down -v
```

## Estructura de Contenedores

| Servicio    | Puerto | Descripción            |
| ----------- | ------ | ---------------------- |
| **app**     | 80     | Laravel + Apache       |
| **mariadb** | 3307   | MariaDB 11             |
| **vite**    | 5173   | Hot Module Replacement |

## Licencia

TODO
