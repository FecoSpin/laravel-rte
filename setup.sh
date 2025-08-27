#!/bin/bash

echo "🚀 Configurando proyecto Laravel con Docker..."

# Construir y levantar los contenedores
echo "📦 Construyendo contenedores..."
docker-compose up -d --build

# Esperar a que los servicios estén listos
echo "⏳ Esperando a que los servicios estén listos..."
sleep 10

# Instalar dependencias
echo "📚 Instalando dependencias de Composer..."
docker-compose exec app composer install --no-interaction

# Generar clave de aplicación
echo "🔑 Generando clave de aplicación..."
docker-compose exec app php artisan key:generate --no-interaction

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
docker-compose exec app php artisan migrate --no-interaction

# Configurar permisos
echo "🔒 Configurando permisos..."
docker-compose exec app chown -R www:www /var/www/storage
docker-compose exec app chown -R www:www /var/www/bootstrap/cache

echo "✅ ¡Proyecto configurado exitosamente!"
echo "🌐 Accede a tu aplicación en: http://localhost:8000"