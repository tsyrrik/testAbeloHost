.DEFAULT_GOAL := help

DC ?= docker compose

help: ## Show this help
	@awk 'BEGIN {FS = ":.*##"; printf "Usage: make <target>\n\nTargets:\n"} /^[a-zA-Z_-]+:.*?##/ {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Build and start the stack in background
	$(DC) up -d --build

down: ## Stop the stack (keeps DB volume)
	$(DC) down

reset: ## Stop the stack and wipe the DB volume
	$(DC) down -v

ps: ## Show container status
	$(DC) ps

logs: ## Tail logs from all services
	$(DC) logs -f --tail=100

php: ## Open a shell in the PHP container
	$(DC) exec php sh

mysql: ## Open a MySQL CLI as root
	$(DC) exec mysql sh -lc 'mysql -uroot -p"$$MYSQL_ROOT_PASSWORD" $$MYSQL_DATABASE'

composer: ## Install PHP dependencies (writes vendor/ and composer.lock to host)
	$(DC) exec php composer install

composer-update: ## Update PHP dependencies
	$(DC) exec php composer update

scss: ## Build CSS once from SCSS (runs on host, needs Node)
	npm run scss

scss-watch: ## Watch SCSS and rebuild on change (host)
	npm run scss:watch

.PHONY: help up down reset ps logs php mysql composer composer-update scss scss-watch
