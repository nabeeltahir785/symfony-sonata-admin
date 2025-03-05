.PHONY: help install start stop restart build bash db-bash db-create db-migrate db-fixtures db-reset cc create-admin create-user assign-role

# Colors
COLOR_RESET = \033[0m
COLOR_INFO = \033[32m
COLOR_COMMENT = \033[33m

## Help command
help:
	@echo "${COLOR_INFO}Sonata Admin Project Makefile${COLOR_RESET}"
	@echo "${COLOR_COMMENT}Usage:${COLOR_RESET}"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2}'

## Install the application
install: ## Install the application
	@echo "${COLOR_INFO}Installing the application...${COLOR_RESET}"
	docker-compose up -d
	docker-compose exec php composer install
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists
	docker-compose exec php php bin/console doctrine:schema:update --force
	docker-compose exec php php bin/console assets:install
	@echo "${COLOR_INFO}Installation completed!${COLOR_RESET}"

## Start the application
start: ## Start the application
	@echo "${COLOR_INFO}Starting the application...${COLOR_RESET}"
	docker-compose up -d

## Stop the application
stop: ## Stop the application
	@echo "${COLOR_INFO}Stopping the application...${COLOR_RESET}"
	docker-compose down

## Restart the application
restart: stop start ## Restart the application

## Rebuild the application
build: ## Rebuild the application
	@echo "${COLOR_INFO}Rebuilding the application...${COLOR_RESET}"
	docker-compose build
	docker-compose up -d

## Enter PHP container
bash: ## Enter PHP container
	@echo "${COLOR_INFO}Entering PHP container...${COLOR_RESET}"
	docker-compose exec php bash

## Enter database container
db-bash: ## Enter database container
	@echo "${COLOR_INFO}Entering database container...${COLOR_RESET}"
	docker-compose exec db bash

## Create database
db-create: ## Create database
	@echo "${COLOR_INFO}Creating database...${COLOR_RESET}"
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists

## Run database migrations
db-migrate: ## Run database migrations
	@echo "${COLOR_INFO}Running database migrations...${COLOR_RESET}"
	docker-compose exec php php bin/console doctrine:schema:update --force

## Load fixtures
db-fixtures: ## Load fixtures
	@echo "${COLOR_INFO}Loading fixtures...${COLOR_RESET}"
	docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction

## Reset database (drop, create, migrate)
db-reset: ## Reset database (drop, create, migrate)
	@echo "${COLOR_INFO}Resetting database...${COLOR_RESET}"
	docker-compose exec php php bin/console doctrine:database:drop --force --if-exists
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists
	docker-compose exec php php bin/console doctrine:schema:update --force
	docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction

## Clear cache
cc: ## Clear cache
	@echo "${COLOR_INFO}Clearing cache...${COLOR_RESET}"
	docker-compose exec php php bin/console cache:clear

## Create admin user
create-admin: ## Create an admin user with ROLE_SUPER_ADMIN
	@echo "${COLOR_INFO}Creating admin user...${COLOR_RESET}"
	docker-compose exec php php bin/console app:create-user --admin

## Create a regular user
create-user: ## Create a regular user
	@echo "${COLOR_INFO}Creating regular user...${COLOR_RESET}"
	docker-compose exec php php bin/console app:create-user

## Assign a role to a user
assign-role: ## Assign a role to a user
	@echo "${COLOR_INFO}Assigning role to user...${COLOR_RESET}"
	docker-compose exec php php bin/console app:assign-role