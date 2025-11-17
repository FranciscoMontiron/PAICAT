# 📊 Monitor de Inicialización de PAICAT

Este script permite visualizar en tiempo real el progreso de inicialización de los contenedores de PAICAT con una barra de progreso y estado detallado de cada paso.

## 🚀 Uso

### Después de `docker-compose up -d`

Cuando ejecutas `docker-compose up -d`, los contenedores se levantan en segundo plano. El proceso de inicialización de PHP puede tardar 1-2 minutos mientras:

1. Instala dependencias de Composer
2. Instala dependencias de NPM
3. Genera la clave de aplicación
4. Limpia caché
5. Ejecuta migraciones
6. Ejecuta seeders
7. Optimiza la aplicación
8. Configura permisos
9. Inicia PHP-FPM

Para ver este progreso en tiempo real, ejecuta:

#### En Windows (PowerShell):
```powershell
.\monitor-startup.ps1
```

#### En Linux/Mac:
```bash
chmod +x monitor-startup.sh
./monitor-startup.sh
```

## 📋 Ejemplo de Salida

```
═══════════════════════════════════════════════════════
   🚀 Monitor de Inicialización de PAICAT
═══════════════════════════════════════════════════════

🔍 Verificando Docker...
✅ Docker está corriendo

🔍 Verificando contenedores...
✅ Contenedores encontrados

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ Instalación de dependencias Composer [DONE]
[███████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░] 15% - Instalación de dependencias Composer
✅ Descubrimiento de paquetes [DONE]
[████████████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░] 25% - Descubrimiento de paquetes
✅ Generación de clave de aplicación [DONE]
[█████████████████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░] 35% - Generación de clave de aplicación
✅ Limpieza de caché [DONE]
[██████████████████████░░░░░░░░░░░░░░░░░░░░░░░░] 45% - Limpieza de caché
✅ Ejecución de migraciones [DONE]
[██████████████████████████████░░░░░░░░░░░░░░░░] 60% - Ejecución de migraciones
✅ Ejecución de seeders [DONE]
[███████████████████████████████████░░░░░░░░░░░] 70% - Ejecución de seeders
✅ Optimización de aplicación [DONE]
[████████████████████████████████████████░░░░░░] 80% - Optimización de aplicación
✅ Creación de enlaces simbólicos [DONE]
[█████████████████████████████████████████████░] 90% - Creación de enlaces simbólicos
✅ Configuración de permisos [DONE]
[███████████████████████████████████████████████] 95% - Configuración de permisos
✅ PHP-FPM iniciado [DONE]
[██████████████████████████████████████████████████] 100% - PHP-FPM iniciado


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   ✨ ¡PAICAT INICIADO EXITOSAMENTE!
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🌐 Aplicación:
   http://localhost

🔑 Credenciales de acceso:
   Email:    admin@paicat.utn.edu.ar
   Password: admin123

📊 Servicios disponibles:
   • PHPMyAdmin: http://localhost:8081
   • Mailhog:    http://localhost:8025
   • Vite HMR:   http://localhost:5173

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

## 🔧 Instalación Automática

Los scripts de instalación (`install.ps1` e `install.sh`) ejecutan automáticamente el monitor después de `docker-compose up -d`:

```powershell
# Windows
.\install.ps1
```

```bash
# Linux/Mac
./install.sh
```

## ⚙️ Uso Manual

Si prefieres iniciar los contenedores manualmente:

```bash
# 1. Levantar contenedores en segundo plano
docker-compose up -d

# 2. Ejecutar monitor (Windows)
.\monitor-startup.ps1

# O en Linux/Mac
./monitor-startup.sh
```

## 🔍 Verificación de Logs

Si quieres ver los logs completos sin el monitor:

```bash
# Ver logs en tiempo real
docker logs paicat_php -f

# Ver últimas 50 líneas
docker logs paicat_php --tail 50

# Ver todos los servicios
docker-compose logs -f
```

## ⚠️ Solución de Problemas

### El monitor no detecta el contenedor

Verifica que los contenedores estén corriendo:
```bash
docker ps
```

Deberías ver contenedores con nombres:
- `paicat_nginx`
- `paicat_php`
- `paicat_mariadb`
- `paicat_redis`
- `paicat_vite`

### El proceso toma demasiado tiempo

Si el monitor reporta timeout:
1. Verifica los logs manualmente: `docker logs paicat_php`
2. Verifica el estado de MariaDB: `docker logs paicat_mariadb`
3. Reinicia los contenedores: `docker-compose restart`

### Error 502 en el navegador

Esto es normal durante los primeros 1-2 minutos mientras PHP-FPM se inicia. Espera a que el monitor muestre "✅ PHP-FPM iniciado [DONE]" antes de acceder a la aplicación.

## 💡 Tips

- Puedes presionar `Ctrl+C` durante el monitoreo para salir sin afectar los contenedores
- El monitor se ejecuta automáticamente al usar los scripts `install.ps1` o `install.sh`
- La primera vez tarda más porque debe descargar imágenes Docker e instalar dependencias
- Los arranques subsiguientes son mucho más rápidos (10-20 segundos)

## 🎨 Características

- ✅ Barra de progreso visual con porcentaje
- ✅ Estado de cada paso (WAITING, RUNNING, DONE, ERROR)
- ✅ Colores para mejor legibilidad
- ✅ Emojis para identificación rápida
- ✅ Timeout de seguridad (2 minutos)
- ✅ Información de acceso al finalizar
- ✅ Compatible con Windows y Linux/Mac
