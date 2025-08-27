#!/bin/bash

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar mensajes con colores
print_message() {
    echo -e "${GREEN}$1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Verificar que Docker esté instalado
check_docker() {
    if ! command -v docker &> /dev/null; then
        print_error "Docker no está instalado. Por favor instala Docker primero."
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose no está instalado. Por favor instala Docker Compose primero."
        exit 1
    fi
    
    print_info "Docker y Docker Compose están instalados ✅"
}

# Verificar que Docker esté corriendo
check_docker_running() {
    if ! docker info &> /dev/null; then
        print_error "Docker no está corriendo. Por favor inicia Docker primero."
        exit 1
    fi
    
    print_info "Docker está corriendo ✅"
}

# Función principal de instalación
main() {
    print_message "🚀 Configurando proyecto Laravel con Docker..."
    echo ""
    
    # Verificaciones previas
    check_docker
    check_docker_running
    
    # Verificar si ya existe un .env
    if [ ! -f .env ]; then
        print_info "Copiando archivo de configuración..."
        cp .env.example .env
    else
        print_warning "El archivo .env ya existe, no se sobrescribirá"
    fi
    
    # Construir y levantar los contenedores
    print_message "📦 Construyendo contenedores..."
    if ! docker-compose up -d --build; then
        print_error "Error al construir los contenedores"
        exit 1
    fi
    
    # Esperar a que los servicios estén listos
    print_message "⏳ Esperando a que los servicios estén listos..."
    sleep 15
    
    # Verificar que los contenedores estén corriendo
    print_info "Verificando estado de contenedores..."
    docker-compose ps
    
    # Instalar dependencias
    print_message "📚 Instalando dependencias de Composer..."
    if ! docker-compose exec app composer install --no-interaction --optimize-autoloader; then
        print_error "Error al instalar dependencias de Composer"
        exit 1
    fi
    
    # Generar clave de aplicación
    print_message "🔑 Generando clave de aplicación..."
    if ! docker-compose exec app php artisan key:generate --no-interaction; then
        print_error "Error al generar la clave de aplicación"
        exit 1
    fi
    
    # Ejecutar migraciones
    print_message "🗄️ Ejecutando migraciones..."
    if ! docker-compose exec app php artisan migrate --no-interaction; then
        print_error "Error al ejecutar migraciones"
        exit 1
    fi
    
    # Configurar permisos (solo en sistemas Unix)
    if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" ]]; then
        print_message "🔒 Configurando permisos..."
        docker-compose exec app chown -R www:www /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
    fi
    
    # Optimizar aplicación
    print_message "⚡ Optimizando aplicación..."
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache
    docker-compose exec app php artisan view:cache
    
    echo ""
    print_message "✅ ¡Proyecto configurado exitosamente!"
    echo ""
    print_info "🌐 Aplicación disponible en: http://localhost:8000"
    print_info "🗄️ Base de datos MySQL: localhost:3306 (usuario: laravel, password: laravel)"
    print_info "🔴 Redis: localhost:6379"
    echo ""
    print_message "📋 Comandos útiles:"
    echo "  docker-compose ps          # Ver estado de contenedores"
    echo "  docker-compose logs -f     # Ver logs en tiempo real"
    echo "  docker-compose down        # Parar contenedores"
    echo "  make help                  # Ver todos los comandos disponibles"
    echo ""
    print_message "🎉 ¡Feliz desarrollo!"
}

# Ejecutar función principal
main