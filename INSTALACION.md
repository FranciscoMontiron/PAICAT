# Guía de Instalación - PAICAT

Manual para configurar el proyecto desde cero después de clonar el repositorio.

##  Requisitos Previos

- **Docker Desktop** instalado y corriendo
- **Git** instalado

---
## Primero debe de dejar en la carpeta /PAICAT el archivo:
```
datos_paicat_completo.sql
```
### EL mismo debe ser solicitado

##  Instalación Automatica

### Windows (PowerShell)

```powershell
# 1. Clonar el repositorio
git clone -b dev https://github.com/tu-usuario/PAICAT.git
cd PAICAT

# 2. Ejecutar script de instalación
.\install.ps1
```
### IMPORTANTE - Al ejecutar install.ps1, al temrinar de ejecutar el script, revisar el log por la instalacion en segundo plano:
```
docker logs paicat_php -f
```
### Para ver el proceso que corre en segundo plano.



### Linux/Mac (Bash)

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/PAICAT.git
cd PAICAT

# 2. Dar permisos y ejecutar
chmod +x install.sh
./install.sh
```

El script automáticamente:
-  Verifica que Docker esté corriendo
-  Copia `.env.example` a `.env`
-  Levanta todos los contenedores
-  Monitorea el progreso de inicio
-  **Importa la base de datos automáticamente** (si existe `datos_paicat_completo.sql`)
-  Muestra las URLs de acceso

> **Nota:** El script convierte automáticamente el archivo SQL a UTF-8 si es necesario (Windows puede guardarlo en UTF-16).

---

##  Verificar Instalación

### Acceder a la aplicación

Abrir en el navegador: **http://localhost**

### Credenciales de prueba

| Usuario | Email | Contraseña | Rol |
|---------|-------|------------|-----|
| Admin | admin@paicat.utn.edu.ar | admin123 | Administrador |
| Coordinador | coordinador@utn.edu.ar | password | Coordinador |
| Docente | docente@utn.edu.ar | password | Docente |

### Verificar base de datos

```powershell
# Conectar a MariaDB
docker exec -it paicat_mariadb mariadb -uroot -proot

# Ver bases de datos
SHOW DATABASES;

# Debería mostrar:
# - paicat
# - alumnos_utn
# - sysacad
```

---

##  Reinstalación Limpia

Si necesitas reinstalar desde cero (por ejemplo, si borraste una base de datos):

```powershell
# Detener y eliminar contenedores + volúmenes
docker-compose down -v

# Ejecutar instalación nuevamente
.\install.ps1
```

> **Importante:** El flag `-v` elimina los volúmenes de datos. Esto borra todas las bases de datos y requiere reimportar el SQL.

---

##  Solución de Problemas

### Error: "Connection refused" a la base de datos

```powershell
# Verificar que MariaDB esté corriendo
docker ps | findstr mariadb

# Reiniciar el contenedor
docker restart paicat_mariadb

# Esperar 10 segundos y probar de nuevo
```

### Error: "SQLSTATE[HY000] [2002]"

El contenedor de MariaDB puede tardar en iniciar. Esperar 30 segundos después de `docker-compose up`.

### Error: Permisos en storage/logs

```powershell
docker exec paicat_php chmod -R 777 storage bootstrap/cache
```

### Error: "Class not found"

```powershell
docker exec paicat_php composer dump-autoload
docker exec paicat_php php artisan config:clear
docker exec paicat_php php artisan cache:clear
```

### Vite no carga los estilos

Verificar que el contenedor Vite esté corriendo:
```powershell
docker logs paicat_vite
```

Si hay error, reiniciar:
```powershell
docker restart paicat_vite
```

---


##  Comandos Útiles

```powershell
# Ver logs de Laravel
docker exec paicat_php tail -f storage/logs/laravel.log

# Ejecutar tinker (consola interactiva)
docker exec -it paicat_php php artisan tinker

# Limpiar cachés
docker exec paicat_php php artisan optimize:clear

# Ver rutas disponibles
docker exec paicat_php php artisan route:list

# Crear nuevo controlador
docker exec paicat_php php artisan make:controller NombreController

# Crear nueva migración
docker exec paicat_php php artisan make:migration create_tabla_table
```

