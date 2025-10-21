# 🚀 Instrucciones de Configuración - Sistema RTE

## 📋 Requisitos Previos

- Docker y Docker Compose instalados
- Git instalado
- Puerto 8000 disponible

## 🔧 Configuración Inicial

### 1. Clonar y Configurar el Proyecto

```bash
# Clonar el repositorio
git clone https://github.com/FecoSpin/laravel-rte.git
cd laravel-rte

# Ejecutar script de configuración automática
# Linux/Mac:
chmod +x setup.sh
./setup.sh

# Windows:
setup.bat
```

### 2. Configuración Manual (Alternativa)

```bash
# Construir y levantar contenedores
docker-compose up -d --build

# Instalar dependencias
docker-compose exec app composer install --no-interaction

# Generar clave de aplicación
docker-compose exec app php artisan key:generate --no-interaction

# Ejecutar migraciones y seeders
docker-compose exec app php artisan migrate --seed --no-interaction

# Configurar permisos (Linux/Mac)
docker-compose exec app chown -R www:www /var/www/storage /var/www/bootstrap/cache
```

## 🗄️ Base de Datos y Datos de Prueba

### Usuarios de Prueba Creados:

| Email | Password | Rol | Descripción |
|-------|----------|-----|-------------|
| admin@rte.com | password | admin | Administrador del sistema |
| supervisor1@rte.com | password | supervisor | Supervisor Zona Norte |
| supervisor2@rte.com | password | supervisor | Supervisor Zona Sur |
| tecnico1@rte.com | password | technician | Técnico Zona Norte |
| tecnico2@rte.com | password | technician | Técnico Zona Sur |

### Datos de Prueba Incluidos:
- ✅ 10 Zonas (5 predefinidas + 5 aleatorias)
- ✅ 25+ Usuarios con diferentes roles
- ✅ 40+ Escuelas distribuidas por zonas
- ✅ 80+ Encuestas RTE con diferentes estados
- ✅ 100+ Solicitudes de mantenimiento

## 🌐 Acceso a la Aplicación

| Servicio | URL | Credenciales |
|----------|-----|--------------|
| **API Backend** | http://localhost:8000/api | Ver tabla de usuarios |
| **Base de datos** | localhost:3306 | laravel / laravel |
| **phpMyAdmin** | http://localhost:8080 | laravel / laravel |
| **Redis** | localhost:6379 | - |

## 🔑 Endpoints Principales

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/user` - Obtener usuario actual

### Recursos Principales
- `GET|POST /api/zones` - Gestión de zonas
- `GET|POST /api/surveys` - Encuestas RTE
- `GET|POST /api/reports` - Reportes RTE
- `GET|POST /api/maintenance-requests` - Solicitudes de mantenimiento
- `GET|POST /api/users` - Gestión de usuarios (Admin)
- `GET /api/dashboard/stats` - Estadísticas del dashboard

## 🧪 Ejecutar Tests

```bash
# Ejecutar todos los tests
docker-compose exec app php artisan test

# Ejecutar tests específicos
docker-compose exec app php artisan test --filter AuthTest

# Ejecutar tests con coverage
docker-compose exec app php artisan test --coverage
```

## 🛠️ Comandos de Desarrollo

```bash
# Ver logs en tiempo real
docker-compose logs -f app

# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Limpiar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear

# Regenerar datos de prueba
docker-compose exec app php artisan migrate:fresh --seed

# Ver rutas disponibles
docker-compose exec app php artisan route:list
```

## 🔒 Configuración de Seguridad

### Variables de Entorno Importantes:
```env
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
```

### Configuración de CORS (si es necesario):
```bash
# Instalar Laravel CORS
docker-compose exec app composer require fruitcake/laravel-cors

# Publicar configuración
docker-compose exec app php artisan vendor:publish --tag="cors"
```

## 📱 Integración con Frontend

### Headers Requeridos:
```javascript
// Para requests autenticados
headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
}
```

### Ejemplo de Login:
```javascript
const response = await fetch('http://localhost:8000/api/auth/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        email: 'admin@rte.com',
        password: 'password'
    })
});

const data = await response.json();
const token = data.token;
```

## 🐛 Solución de Problemas

### Problema: Contenedores no inician
```bash
# Verificar Docker
docker --version
docker-compose --version

# Limpiar y reconstruir
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

### Problema: Errores de permisos
```bash
# Linux/Mac
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Dentro del contenedor
docker-compose exec app chown -R www:www /var/www/storage /var/www/bootstrap/cache
```

### Problema: Base de datos no conecta
```bash
# Verificar estado de contenedores
docker-compose ps

# Ver logs de la base de datos
docker-compose logs db

# Recrear base de datos
docker-compose exec app php artisan migrate:fresh --seed
```

## 📚 Documentación Adicional

- [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) - Documentación completa de la API
- [README.md](./README.md) - Información general del proyecto
- [Postman Collection](./postman_collection.json) - Colección para pruebas de API

## 🎯 Próximos Pasos

1. **Frontend**: Desarrollar interfaz de usuario con React/Vue/Angular
2. **Notificaciones**: Implementar sistema de notificaciones en tiempo real
3. **PDF**: Completar generación de reportes PDF
4. **Móvil**: Desarrollar aplicación móvil para técnicos
5. **Analytics**: Agregar dashboards avanzados con gráficos
