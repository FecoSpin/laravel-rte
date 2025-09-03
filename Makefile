# 🚀 Laravel Docker - Comandos Rápidos

.PHONY: help install up down build logs shell test clean

# Colores para output
GREEN=\033[0;32m
YELLOW=\033[1;33m
RED=\033[0;31m
NC=\033[0m # No Color

help: ## 📋 Mostrar ayuda
	@echo "$(GREEN)🚀 Laravel Docker - Comandos Disponibles$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(YELLOW)%-20s$(NC) %s\n", $$1, $$2}'

install: ## 🏗️ Instalación completa del proyecto
	@echo "$(GREEN)🏗️ Instalando proyecto...$(NC)"
	docker-compose up -d --build
	docker-compose exec app composer install --no-interaction
	docker-compose exec app php artisan key:generate --no-interaction
	docker-compose exec app php artisan migrate --no-interaction
	@echo "$(GREEN)✅ Instalación completada!$(NC)"
	@echo "$(YELLOW)🌐 Aplicación disponible en: http://localhost:8000$(NC)"

up: ## 🚀 Levantar contenedores
	@echo "$(GREEN)🚀 Levantando contenedores...$(NC)"
	docker-compose up -d

down: ## 🛑 Parar contenedores
	@echo "$(RED)🛑 Parando contenedores...$(NC)"
	docker-compose down

build: ## 🔨 Reconstruir imágenes
	@echo "$(GREEN)🔨 Reconstruyendo imágenes...$(NC)"
	docker-compose build --no-cache

logs: ## 📋 Ver logs en tiempo real
	docker-compose logs -f

shell: ## 🐚 Acceder al contenedor de Laravel
	docker-compose exec app bash

mysql: ## 🗄️ Acceder a MySQL
	docker-compose exec db mysql -u laravel -p laravel

redis: ## 🔴 Acceder a Redis CLI
	docker-compose exec redis redis-cli

test: ## 🧪 Ejecutar tests
	@echo "$(GREEN)🧪 Ejecutando tests...$(NC)"
	docker-compose exec app php artisan test

test-coverage: ## 📊 Ejecutar tests con coverage
	@echo "$(GREEN)📊 Ejecutando tests con coverage...$(NC)"
	docker-compose exec app php artisan test --coverage

lint: ## 🎨 Verificar estilo de código
	@echo "$(GREEN)🎨 Verificando estilo de código...$(NC)"
	docker-compose exec app ./vendor/bin/pint --test

lint-fix: ## 🔧 Arreglar estilo de código
	@echo "$(GREEN)🔧 Arreglando estilo de código...$(NC)"
	docker-compose exec app ./vendor/bin/pint

analyze: ## 🔍 Análisis estático con PHPStan
	@echo "$(GREEN)🔍 Ejecutando análisis estático...$(NC)"
	docker-compose exec app ./vendor/bin/phpstan analyse

migrate: ## 🗄️ Ejecutar migraciones
	docker-compose exec app php artisan migrate

migrate-fresh: ## 🔄 Recrear base de datos
	docker-compose exec app php artisan migrate:fresh --seed

cache-clear: ## 🧹 Limpiar cache
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear
	docker-compose exec app php artisan cache:clear

optimize: ## ⚡ Optimizar aplicación
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache
	docker-compose exec app php artisan optimize

clean: ## 🧹 Limpiar todo (contenedores, volúmenes, imágenes)
	@echo "$(RED)⚠️  CUIDADO: Esto eliminará todos los datos!$(NC)"
	@read -p "¿Estás seguro? (y/N): " confirm && [ "$$confirm" = "y" ]
	docker-compose down -v
	docker system prune -a -f

status: ## 📊 Ver estado de contenedores
	docker-compose ps

composer-install: ## 📦 Instalar dependencias de Composer
	docker-compose exec app composer install

composer-update: ## 🔄 Actualizar dependencias de Composer
	docker-compose exec app composer update

artisan: ## 🎨 Ejecutar comando Artisan (uso: make artisan CMD="route:list")
	docker-compose exec app php artisan $(CMD)

npm-install: ## 📦 Instalar dependencias de NPM
	docker-compose exec app npm install

npm-dev: ## 🔧 Ejecutar NPM dev
	docker-compose exec app npm run dev

npm-build: ## 🏗️ Build de producción con NPM
	docker-compose exec app npm run build

backup-db: ## 💾 Backup de base de datos
	@echo "$(GREEN)💾 Creando backup de base de datos...$(NC)"
	docker-compose exec db mysqldump -u laravel -p laravel laravel > backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)✅ Backup creado!$(NC)"

# Comando por defecto
.DEFAULT_GOAL := help