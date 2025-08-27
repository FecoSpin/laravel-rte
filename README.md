# Laravel con Docker

Este es un proyecto Laravel completamente dockerizado con Nginx, MySQL y Redis.

## Requisitos

- Docker
- Docker Compose

## Instalación y Configuración

1. Clona el repositorio
2. Ejecuta los contenedores:

```bash
docker-compose up -d
```

3. Instala las dependencias de Composer:

```bash
docker-compose exec app composer install
```

4. Genera la clave de aplicación:

```bash
docker-compose exec app php artisan key:generate
```

5. Ejecuta las migraciones:

```bash
docker-compose exec app php artisan migrate
```

## Acceso a la aplicación

- **Aplicación web**: http://localhost:8000
- **Base de datos MySQL**: localhost:3306
- **Redis**: localhost:6379

## Comandos útiles

### Artisan
```bash
docker-compose exec app php artisan [comando]
```

### Composer
```bash
docker-compose exec app composer [comando]
```

### Acceder al contenedor
```bash
docker-compose exec app bash
```

### Ver logs
```bash
docker-compose logs -f [servicio]
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

## Desarrollo

Para desarrollo, puedes usar los siguientes comandos:

```bash
# Levantar los servicios
docker-compose up -d

# Ver logs en tiempo real
docker-compose logs -f

# Parar los servicios
docker-compose down

# Reconstruir las imágenes
docker-compose build --no-cache
```