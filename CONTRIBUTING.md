# 🤝 Guía de Contribución

¡Gracias por tu interés en contribuir a este proyecto! Esta guía te ayudará a empezar.

## 🚀 Configuración del Entorno de Desarrollo

### 1. Fork y Clona el Repositorio
```bash
# Fork el repositorio en GitHub, luego clona tu fork
git clone https://github.com/TU-USUARIO/laravel-rte.git
cd laravel-rte

# Agrega el repositorio original como upstream
git remote add upstream https://github.com/FecoSpin/laravel-rte.git
```

### 2. Configuración con Docker
```bash
# Ejecuta el script de configuración
./setup.sh  # Linux/Mac
# o
setup.bat   # Windows

# O manualmente:
docker-compose up -d --build
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

### 3. Verifica que Todo Funcione
```bash
# Ejecuta los tests
docker-compose exec app php artisan test

# Verifica el código
docker-compose exec app ./vendor/bin/pint --test
docker-compose exec app ./vendor/bin/phpstan analyse
```

## 📝 Proceso de Contribución

### 1. Crea una Rama para tu Feature
```bash
git checkout -b feature/nombre-descriptivo
# o
git checkout -b fix/descripcion-del-bug
```

### 2. Realiza tus Cambios
- Escribe código limpio y bien documentado
- Sigue las convenciones de Laravel
- Agrega tests para nuevas funcionalidades
- Actualiza la documentación si es necesario

### 3. Ejecuta las Pruebas
```bash
# Tests unitarios y de integración
docker-compose exec app php artisan test

# Verificación de estilo de código
docker-compose exec app ./vendor/bin/pint

# Análisis estático
docker-compose exec app ./vendor/bin/phpstan analyse
```

### 4. Commit y Push
```bash
# Commits descriptivos usando conventional commits
git add .
git commit -m "feat: agregar nueva funcionalidad X"
git push origin feature/nombre-descriptivo
```

### 5. Crea un Pull Request
- Ve a GitHub y crea un Pull Request
- Describe claramente qué cambios realizaste
- Referencia issues relacionados si los hay
- Asegúrate de que pasen todos los checks de CI/CD

## 🎯 Tipos de Contribuciones

### 🐛 Reportar Bugs
- Usa el template de issue para bugs
- Incluye pasos para reproducir el problema
- Especifica tu entorno (OS, versión de Docker, etc.)

### ✨ Nuevas Funcionalidades
- Abre un issue primero para discutir la funcionalidad
- Asegúrate de que esté alineada con los objetivos del proyecto
- Incluye tests y documentación

### 📚 Documentación
- Mejoras en README, comentarios en código
- Ejemplos de uso
- Guías de configuración

### 🔧 Refactoring
- Mejoras en el código existente
- Optimizaciones de rendimiento
- Actualización de dependencias

## 📋 Estándares de Código

### PHP/Laravel
- Sigue [PSR-12](https://www.php-fig.org/psr/psr-12/)
- Usa [Laravel Pint](https://laravel.com/docs/pint) para formateo
- Nombres descriptivos para variables y funciones
- Comentarios en inglés

### Commits
Usa [Conventional Commits](https://www.conventionalcommits.org/):
```
feat: nueva funcionalidad
fix: corrección de bug
docs: cambios en documentación
style: formateo, punto y coma faltante, etc.
refactor: refactoring de código
test: agregar tests
chore: actualizar dependencias, etc.
```

### Tests
- Escribe tests para nuevas funcionalidades
- Mantén cobertura de tests alta
- Usa nombres descriptivos para tests
- Organiza tests en Feature y Unit

## 🔍 Revisión de Código

### Antes de Enviar PR
- [ ] Tests pasan localmente
- [ ] Código formateado con Pint
- [ ] PHPStan no reporta errores
- [ ] Documentación actualizada
- [ ] Commits bien estructurados

### Durante la Revisión
- Responde a comentarios constructivamente
- Realiza cambios solicitados
- Mantén la conversación profesional y amigable

## 🚀 Configuración de CI/CD

El proyecto usa GitHub Actions para:
- ✅ Ejecutar tests automáticamente
- ✅ Verificar estilo de código
- ✅ Análisis estático con PHPStan
- ✅ Build de Docker

## 📞 ¿Necesitas Ayuda?

- 💬 Abre un issue con la etiqueta "question"
- 📧 Contacta al mantenedor: f.espinoza@whipple.mx
- 📖 Revisa la documentación en [INSTRUCCIONES.md](INSTRUCCIONES.md)

## 🎉 Reconocimientos

Todos los contribuidores serán reconocidos en el README del proyecto.

¡Gracias por contribuir! 🙏