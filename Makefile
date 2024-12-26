#---Symfony-And-Docker-Makefile---------------#
# Author: https://github.com/yoanbernabeu
# License: MIT
#---------------------------------------------#

#---VARIABLES---------------------------------#
#---DOCKER---#
DOCKER = docker
DOCKER_RUN = $(DOCKER) run
DOCKER_COMPOSE = docker compose
DOCKER_COMPOSE_UP = $(DOCKER_COMPOSE) up -d
DOCKER_COMPOSE_STOP = $(DOCKER_COMPOSE) stop
#------------#

#---SYMFONY--#
SYMFONY = symfony
SYMFONY_SERVER_START = $(SYMFONY) serve -d
SYMFONY_SERVER_STOP = $(SYMFONY) server:stop
SYMFONY_CONSOLE = $(SYMFONY) console
SYMFONY_LINT = $(SYMFONY_CONSOLE) lint:
#------------#

#---COMPOSER-#
COMPOSER = composer
COMPOSER_INSTALL = $(COMPOSER) install
COMPOSER_UPDATE = $(COMPOSER) update
#------------#

#---PHPQA---#
PHPQA = jakzal/phpqa:php8.3
PHPQA_RUN = $(DOCKER_RUN) --init --rm -v $(PWD):/project -w /project $(PHPQA)
#------------#

#---PHPUNIT-#
PHPUNIT = APP_ENV=test $(SYMFONY) php bin/phpunit
#------------#

#---MKDOCS--#
MKDOCS = squidfunk/mkdocs-material
MKDOCS_RUN = $(DOCKER_RUN) --rm -it -p 8000:8000 -v $(PWD)/.docs:/docs $(MKDOCS)
#------------#
#---------------------------------------------# s

## === 🆘  HELP ==================================================
help: ## Show this help.
	@echo "Symfony-And-Docker-Makefile"
	@echo "---------------------------"
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
#---------------------------------------------#

## === 🎛️  SYMFONY ===============================================
sf-start: ## Start symfony server.
	$(SYMFONY_SERVER_START)
.PHONY: sf-start

sf-stop: ## Stop symfony server.
	$(SYMFONY_SERVER_STOP)
.PHONY: sf-stop

sf-cc: ## Clear symfony cache.
	$(SYMFONY_CONSOLE) cache:clear
.PHONY: sf-cc

sf-log: ## Show symfony logs.
	$(SYMFONY) server:log
.PHONY: sf-log

sf-dc: ## Create symfony database.
	$(SYMFONY_CONSOLE) doctrine:database:create
.PHONY: sf-dc

sf-dd-test: ## Drop symfony database in test environment.
	$(SYMFONY_CONSOLE) doctrine:database:drop --force --env=test
.PHONY: sf-dd-test

sf-dc-test: ## Create symfony database in test environment.
	$(SYMFONY_CONSOLE) doctrine:database:create --env=test
.PHONY: sf-dc-test

sf-dd: ## Drop symfony database.
	$(SYMFONY_CONSOLE) doctrine:database:drop --force
.PHONY: sf-dd

sf-mm: ## Make migrations.
	$(SYMFONY_CONSOLE) make:migration
.PHONY: sf-mm

sf-dmm: ## Migrate.
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction
.PHONY: sf-dmm

sf-dmm-test: ## Migrate in test environment.
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --env=test --no-interaction
.PHONY: sf-dmm-test

sf-dump-env: ## Dump env.
	$(SYMFONY_CONSOLE) debug:dotenv
.PHONY: sf-dump-env

sf-dump-env-container: ## Dump Env container.
	$(SYMFONY_CONSOLE) debug:container --env-vars
.PHONY: sf-dump-env-container

sf-dump-routes: ## Dump routes.
	$(SYMFONY_CONSOLE) debug:router
.PHONY: sf-dump-routes

sf-open: ## Open symfony server.
	$(SYMFONY) open:local
.PHONY: sf-open

sf-check-requirements: ## Check requirements.
	$(SYMFONY) check:requirements
.PHONY: sf-check-requirements
#---------------------------------------------#

## === 📦  COMPOSER ==============================================
composer-install: ## Install composer dependencies.
	$(COMPOSER_INSTALL)
.PHONY: composer-install

composer-update: ## Update composer dependencies.
	$(COMPOSER_UPDATE)
.PHONY: composer-update
#---------------------------------------------#

## === 📦  NPM =================================================
npm-install: ## Install npm dependencies.
	$(DOCKER_RUN) --init --rm -v $(PWD):/app -w /app node:22 npm install
.PHONY: npm-install
#---------------------------------------------#

## === 🐛  PHPQA =================================================
qa-cs-fixer-dry-run: ## Run php-cs-fixer in dry-run mode.
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose --dry-run
.PHONY: qa-cs-fixer-dry-run

qa-cs-fixer: ## Run php-cs-fixer.
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose
.PHONY: qa-cs-fixer

qa-phpstan: ## Run phpstan.
	$(PHPQA_RUN) phpstan analyse ./src --level=8
.PHONY: qa-phpstan

qa-security-checker: ## Run security-checker.
	$(SYMFONY) security:check
.PHONY: qa-security-checker

qa-phpcpd: ## Run phpcpd (copy/paste detector).
	$(PHPQA_RUN) phpcpd ./src
.PHONY: qa-phpcpd

qa-php-metrics: ## Run php-metrics.
	$(PHPQA_RUN) phpmetrics --report-html=var/phpmetrics ./src
.PHONY: qa-php-metrics

qa-lint-twigs: ## Lint twig files.
	$(SYMFONY_LINT)twig ./templates
.PHONY: qa-lint-twigs

qa-lint-yaml: ## Lint yaml files.
	$(SYMFONY_LINT)yaml ./config
.PHONY: qa-lint-yaml

qa-lint-container: ## Lint container.
	$(SYMFONY_LINT)container
.PHONY: qa-lint-container

qa-lint-schema: ## Lint Doctrine schema.
	$(SYMFONY_CONSOLE) doctrine:schema:validate --skip-sync -vvv --no-interaction
.PHONY: qa-lint-schema
#---------------------------------------------#

## === 📚 DOCUMENTATION =========================================
docs-serve: ## Serve documentation locally
	$(MKDOCS_RUN) serve -a 0.0.0.0:8000
.PHONY: docs-serve

docs-build: ## Build documentation
	$(MKDOCS_RUN) build
.PHONY: docs-build
#---------------------------------------------#

## === 🔎  TESTS =================================================
tests: ## Run tests.
	$(MAKE) sf-dd-test
	$(MAKE) sf-dc-test
	$(MAKE) sf-dmm-test
	$(PHPUNIT) --testdox
.PHONY: tests

tests-coverage: ## Run tests with coverage.
	$(MAKE) sf-dc-test
	$(MAKE) sf-dmm-test
	XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html ./var/coverage
.PHONY: tests-coverage
#---------------------------------------------#

## === 🚀 PROJECT ===============================================

deploy: ## Deploy project.
	@cp compose.yaml compose.prod.yaml
	@read -p "Enter SERVER_NAME (e.g. example.com or :80 for no SSL): " SERVER_NAME; \
	if [ "$$SERVER_NAME" = ":80" ]; then \
		sed -i'.bak' 's/      - SERVER_NAME=.*$$/      - SERVER_NAME=:80 # without ssl\/letsencrypt/' compose.prod.yaml; \
	else \
		sed -i'.bak' 's/      - SERVER_NAME=.*$$/      - SERVER_NAME='"$$SERVER_NAME"' # for prod with ssl\/letsencrypt/' compose.prod.yaml; \
	fi; \
	rm -f compose.prod.yaml.bak
	$(DOCKER_COMPOSE) -f compose.prod.yaml up -d
	$(DOCKER_COMPOSE) -f compose.prod.yaml exec openstreampoll php bin/console doctrine:database:create
	$(DOCKER_COMPOSE) -f compose.prod.yaml exec openstreampoll php bin/console doctrine:migrations:migrate --no-interaction
	@read -p "Enter username: " USER; \
	read -p "Enter password: " PASSWORD; \
	$(DOCKER_COMPOSE) -f compose.prod.yaml exec openstreampoll php bin/console app:create-user $$USER $$PASSWORD
.PHONY: deploy

start: ## Start project for local development.
	$(MAKE) sf-dmm
	$(MAKE) sf-start
	$(MAKE) sf-open
	$(SYMFONY_CONSOLE) tailwind:build --watch
.PHONY: start
#---------------------------------------------#

## === ⭐  OTHER =================================================
before-commit: ## Run before commit.
	$(MAKE) qa-cs-fixer
	$(MAKE) qa-phpstan
	$(MAKE) qa-security-checker
	$(MAKE) qa-lint-twigs
	$(MAKE) qa-lint-yaml
	$(MAKE) qa-lint-container
	$(MAKE) qa-lint-schema
	$(MAKE) tests
.PHONY: before-commit

first-install: ## First install.
	$(MAKE) composer-install
	$(MAKE) npm-install
	$(MAKE) sf-dc
	$(MAKE) start
.PHONY: first-install

stop: ## Stop project.
	$(MAKE) sf-stop
.PHONY: stop

reset-db: ## Reset database.
	$(eval CONFIRM := $(shell read -p "Are you sure you want to reset the database? [y/N] " CONFIRM && echo $${CONFIRM:-N}))
	@if [ "$(CONFIRM)" = "y" ]; then \
		$(MAKE) sf-dd; \
		$(MAKE) sf-dc; \
		$(MAKE) sf-dmm; \
	fi
.PHONY: reset-db
#---------------------------------------------#