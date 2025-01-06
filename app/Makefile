.PHONY: test clean all
# Inspired from https://www.strangebuzz.com/en/snippets/the-perfect-makefile-for-symfony
# Inspired from https://www.strangebuzz.com/en/snippets/the-perfect-makefile-for-symfony

USER_ID:=$(shell id -u)
GROUP_ID:=$(shell id -g)

export USER_ID
export GROUP_ID

# Alias
DOCKER = docker
PHP_CONTAINER = base
EXEC_DOCKER = ${DOCKER} container exec -it ${PHP_CONTAINER}
CONSOLE = ${EXEC_DOCKER} bin/console
COMPOSER = ${EXEC_DOCKER} composer

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'


## —— Install & deploy ————————————————————————————————————————————————————————————
install: ## Install the composer dependencies from back to front
	${COMPOSER} install
	${CONSOLE} importmap:install

compile: ## compile assets from asset mapper
	${CONSOLE} tailwind:build --minify
	${CONSOLE} asset-map:compile

watch: ## watch for tailwind classes
	${CONSOLE} tailwind:build --watch

ts: ## watch for typescript changes
	${CONSOLE} typescript:build --watch


