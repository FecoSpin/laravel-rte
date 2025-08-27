# 🚀 Proyecto Laravel Dockerizado - Instrucciones de Uso

## ✅ Estado del Proyecto
Tu proyecto Laravel con Docker está **completamente configurado y funcionando**.

## 🌐 Acceso a la Aplicación
- **URL de la aplicación**: http://localhost:8000
- **Base de datos MySQL**: localhost:3306
- **Redis**: localhost:6379

## 📋 Comandos Útiles

### Gestión de Contenedores
```bash
# Levantar todos los servicios
docker-compose up -d

# Ver el estado de los contenedores
docker-compose ps

# Ver logs en tiempo real
docker-compose logs -f

# Parar todos los servicios
docker-compose down

# Reconstruir las imágenes
docker-compose build --no-cache
```

### Comandos de Laravel
```bash
# Ejecutar comandos Artisan
docker-compose exec app php artisan [comando]

# Ejemplos específicos:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller UserController
docker-compose exec app php artisan route:list
docker-compose exec app php artisan tinker
```

### Gestión de Dependencias
```bash
# Instalar dependencias de Composer
docker-compose exec app composer install

# Actualizar dependencias
docker-compose exec app composer update

# Instalar una nueva dependencia
docker-compose exec app composer require [paquete]
```

### Acceso a Contenedores
```bash
# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Acceder a MySQL
docker-compose exec db mysql -u laravel -p laravel

# Acceder a Redis CLI
docker-compose exec redis redis-cli
```

## 🗂️ Estructura del Proyecto

```
├── docker/
│   └── nginx/
│       └── default.conf    # Configuración de Nginx
├── docker-compose.yml      # Configuración de servicios
├── Dockerfile             # Imagen de la aplicación
├── .dockerignore          # Archivos ignorados por Docker
├── setup.sh              # Script de configuración (Linux/Mac)
├── setup.bat             # Script de configuración (Windows)
└── [archivos de Laravel]
```

## 🔧 Configuración de Base de Datos

Las credenciales de la base de datos están configuradas en el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

## 🚨 Solución de Problemas

### Si los contenedores no inician:
```bash
docker-compose down
docker-compose up -d --build
```

### Si hay problemas de permisos:
```bash
# En Linux/Mac
sudo chown -R $USER:$USER storage bootstrap/cache

# En Windows, los permisos se manejan automáticamente
```

### Si la aplicación no carga:
1. Verifica que todos los contenedores estén corriendo: `docker-compose ps`
2. Revisa los logs: `docker-compose logs -f`
3. Asegúrate de que el puerto 8000 no esté ocupado

## 📝 Próximos Pasos

1. **Desarrollo**: Comienza a desarrollar tu aplicación Laravel
2. **Base de datos**: Crea tus modelos y migraciones
3. **Frontend**: Configura Vite para el frontend si es necesario
4. **Testing**: Ejecuta las pruebas con `docker-compose exec app php artisan test`

## 🎉 ¡Listo para Desarrollar!

Tu entorno de desarrollo Laravel con Docker está completamente configurado. Puedes empezar a desarrollar tu aplicación accediendo a http://localhost:8000

¿Necesitas ayuda con algo específico? ¡Pregúntame!