@echo off
setlocal enabledelayedexpansion

echo.
echo 🚀 Configurando proyecto Laravel con Docker...
echo.

REM Verificar que Docker esté instalado
docker --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker no está instalado. Por favor instala Docker primero.
    pause
    exit /b 1
)

docker-compose --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker Compose no está instalado. Por favor instala Docker Compose primero.
    pause
    exit /b 1
)

echo ℹ️  Docker y Docker Compose están instalados ✅

REM Verificar que Docker esté corriendo
docker info >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker no está corriendo. Por favor inicia Docker primero.
    pause
    exit /b 1
)

echo ℹ️  Docker está corriendo ✅

REM Verificar si ya existe un .env
if not exist .env (
    echo ℹ️  Copiando archivo de configuración...
    copy .env.example .env >nul
) else (
    echo ⚠️  El archivo .env ya existe, no se sobrescribirá
)

REM Construir y levantar los contenedores
echo 📦 Construyendo contenedores...
docker-compose up -d --build
if errorlevel 1 (
    echo ❌ Error al construir los contenedores
    pause
    exit /b 1
)

REM Esperar a que los servicios estén listos
echo ⏳ Esperando a que los servicios estén listos...
timeout /t 15 /nobreak > nul

REM Verificar que los contenedores estén corriendo
echo ℹ️  Verificando estado de contenedores...
docker-compose ps

REM Instalar dependencias
echo 📚 Instalando dependencias de Composer...
docker-compose exec app composer install --no-interaction --optimize-autoloader
if errorlevel 1 (
    echo ❌ Error al instalar dependencias de Composer
    pause
    exit /b 1
)

REM Generar clave de aplicación
echo 🔑 Generando clave de aplicación...
docker-compose exec app php artisan key:generate --no-interaction
if errorlevel 1 (
    echo ❌ Error al generar la clave de aplicación
    pause
    exit /b 1
)

REM Ejecutar migraciones
echo 🗄️ Ejecutando migraciones...
docker-compose exec app php artisan migrate --no-interaction
if errorlevel 1 (
    echo ❌ Error al ejecutar migraciones
    pause
    exit /b 1
)

REM Optimizar aplicación
echo ⚡ Optimizando aplicación...
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo.
echo ✅ ¡Proyecto configurado exitosamente!
echo.
echo ℹ️  🌐 Aplicación disponible en: http://localhost:8000
echo ℹ️  🗄️ Base de datos MySQL: localhost:3306 (usuario: laravel, password: laravel)
echo ℹ️  🔴 Redis: localhost:6379
echo.
echo 📋 Comandos útiles:
echo   docker-compose ps          # Ver estado de contenedores
echo   docker-compose logs -f     # Ver logs en tiempo real
echo   docker-compose down        # Parar contenedores
echo.
echo 🎉 ¡Feliz desarrollo!
echo.
pause