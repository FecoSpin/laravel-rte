# 🚀 Laravel con Docker

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-20.10+-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docker.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Redis](https://img.shields.io/badge/Redis-7.0-DC382D?style=for-the-badge&logo=redis&logoColor=white)](https://redis.io)
[![Nginx](https://img.shields.io/badge/Nginx-1.25-009639?style=for-the-badge&logo=nginx&logoColor=white)](https://nginx.org)

Un proyecto Laravel completamente dockerizado con Nginx, MySQL y Redis, listo para desarrollo y producción.

## 📋 Requisitos

- [Docker](https://docs.docker.com/get-docker/) 20.10+
- [Docker Compose](https://docs.docker.com/compose/install/) 2.0+
- [Git](https://git-scm.com/downloads)

## 🚀 Instalación Rápida

### Opción 1: Script Automático (Recomendado)

```bash
# Clonar el repositorio
git clone https://github.com/FecoSpin/laravel-rte.git
cd laravel-rte

# Linux/Mac
chmod +x setup.sh
./setup.sh

# Windows
setup.bat
```

### Opción 2: Instalación Manual

```bash
# 1. Clonar el repositorio
git clone https://github.com/FecoSpin/laravel-rte.git
cd laravel-rte

# 2. Construir y levantar contenedores
docker-compose up -d --build

# 3. Instalar dependencias
docker-compose exec app composer install --no-interaction

# 4. Generar clave de aplicación
docker-compose exec app php artisan key:generate --no-interaction

# 5. Ejecutar migraciones
docker-compose exec app php artisan migrate --no-interaction

# 6. Configurar permisos (solo Linux/Mac)
docker-compose exec app chown -R www:www /var/www/storage /var/www/bootstrap/cache
```

## ✅ Verificación

Después de la instalación, verifica que todo funcione:

```bash
# Ver estado de contenedores
docker-compose ps

# Verificar logs
docker-compose logs -f

# Probar la aplicación
curl http://localhost:8000
```

## 🏗️ Arquitectura del Proyecto

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Nginx:80      │    │  Laravel:9000   │    │   MySQL:3306    │
│   (Webserver)   │◄──►│   (PHP-FPM)     │◄──►│   (Database)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                ▲
                                │
                       ┌─────────────────┐
                       │   Redis:6379    │
                       │    (Cache)      │
                       └─────────────────┘
```

## 🌐 Acceso a los Servicios

| Servicio | URL/Puerto | Credenciales |
|----------|------------|--------------|
| **Aplicación Web** | http://localhost:8000 | - |
| **Base de datos MySQL** | localhost:3306 | `laravel` / `laravel` |
| **Redis** | localhost:6379 | - |
| **phpMyAdmin** (opcional) | http://localhost:8080 | `laravel` / `laravel` |

## 🛠️ Comandos de Desarrollo

### Gestión de Contenedores
```bash
# Levantar todos los servicios
docker-compose up -d

# Levantar con logs en tiempo real
docker-compose up

# Parar todos los servicios
docker-compose down

# Reconstruir imágenes
docker-compose build --no-cache

# Ver estado de contenedores
docker-compose ps

# Ver logs de todos los servicios
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f app
docker-compose logs -f webserver
docker-compose logs -f db
docker-compose logs -f redis
```

### Comandos Laravel (Artisan)
```bash
# Comandos básicos
docker-compose exec app php artisan migrate
docker-compose exec app php artisan migrate:rollback
docker-compose exec app php artisan migrate:fresh --seed
docker-compose exec app php artisan db:seed

# Generar archivos
docker-compose exec app php artisan make:controller UserController
docker-compose exec app php artisan make:model Product -m
docker-compose exec app php artisan make:migration create_products_table
docker-compose exec app php artisan make:seeder ProductSeeder

# Cache y optimización
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan optimize

# Limpiar cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear

# Otros comandos útiles
docker-compose exec app php artisan route:list
docker-compose exec app php artisan tinker
docker-compose exec app php artisan queue:work
docker-compose exec app php artisan schedule:run
```

### Gestión de Dependencias
```bash
# Composer
docker-compose exec app composer install
docker-compose exec app composer update
docker-compose exec app composer require laravel/sanctum
docker-compose exec app composer require --dev laravel/pint

# NPM (si necesitas frontend)
docker-compose exec app npm install
docker-compose exec app npm run dev
docker-compose exec app npm run build
```

### Acceso a Contenedores
```bash
# Acceder al contenedor de Laravel
docker-compose exec app bash

# Acceder a MySQL
docker-compose exec db mysql -u laravel -p laravel

# Acceder a Redis CLI
docker-compose exec redis redis-cli

# Ejecutar comandos directamente
docker-compose exec app ls -la
docker-compose exec app php -v
docker-compose exec db mysql --version
```

### Testing
```bash
# Ejecutar tests
docker-compose exec app php artisan test

# Ejecutar tests específicos
docker-compose exec app php artisan test --filter UserTest

# Ejecutar tests con coverage
docker-compose exec app php artisan test --coverage
```

## Servicios incluidos

- **app**: Aplicación Laravel con PHP 8.2-FPM
- **webserver**: Nginx
- **db**: MySQL 8.0
- **redis**: Redis Alpine

## Estructura del proyecto

```
├── docker/
│   └── nginx/
│       └── default.conf    # Configuración de Nginx
├── docker-compose.yml      # Configuración de servicios
├── Dockerfile             # Imagen de la aplicación
└── .dockerignore          # Archivos ignorados por Docker
```

## 🔧 Solución de Problemas

### Problemas Comunes

#### Los contenedores no inician
```bash
# Verificar que Docker esté corriendo
docker --version
docker-compose --version

# Limpiar y reconstruir
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

#### Error de permisos (Linux/Mac)
```bash
# Arreglar permisos de storage
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

#### La aplicación no carga
```bash
# Verificar que todos los contenedores estén corriendo
docker-compose ps

# Verificar logs de errores
docker-compose logs -f app
docker-compose logs -f webserver

# Verificar que el puerto 8000 no esté ocupado
netstat -tulpn | grep :8000  # Linux/Mac
netstat -an | findstr :8000  # Windows
```

#### Error de base de datos
```bash
# Verificar conexión a MySQL
docker-compose exec db mysql -u laravel -p laravel

# Recrear base de datos
docker-compose exec app php artisan migrate:fresh --seed
```

#### Limpiar todo y empezar de nuevo
```bash
# ⚠️ CUIDADO: Esto eliminará todos los datos
docker-compose down -v
docker system prune -a
git pull origin main
docker-compose up -d --build
```

## 🚀 Despliegue en Producción

### Variables de Entorno
Crea un archivo `.env.production`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_HOST=tu-servidor-mysql
DB_DATABASE=tu-base-datos
DB_USERNAME=tu-usuario
DB_PASSWORD=tu-password-seguro

REDIS_HOST=tu-servidor-redis
```

### Comandos de Producción
```bash
# Optimizar para producción
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan optimize

# Ejecutar migraciones en producción
docker-compose exec app php artisan migrate --force
```

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Changelog

### [1.0.0] - 2025-08-27
- ✅ Configuración inicial de Laravel 12 con Docker
- ✅ Servicios: Nginx, MySQL 8.0, Redis
- ✅ Scripts de instalación automática
- ✅ Documentación completa

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 👨‍💻 Autor

**FecoSpin** - [GitHub](https://github.com/FecoSpin)

## ⭐ ¿Te gustó el proyecto?

¡Dale una estrella en GitHub! ⭐