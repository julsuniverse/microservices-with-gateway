init: docker-up-and-build composer-install data-migrations-migrate data-fixtures auth-migrations-migrate auth-create-oauth-client

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-build:
	docker-compose build

docker-up-and-build:
	docker-compose up -d --build

data-migrations-diff:
	docker-compose run --rm data-php-cli php bin/console doctrine:migrations:diff

data-migrations-migrate:
	docker-compose run --rm data-php-cli php bin/console doctrine:migrations:migrate --no-interaction

data-fixtures:
	docker-compose run --rm data-php-cli php bin/console doctrine:fixtures:load --no-interaction

auth-migrations-diff:
	docker-compose run --rm auth-php-cli php bin/console doctrine:migrations:diff

auth-migrations-migrate:
	docker-compose run --rm auth-php-cli php bin/console doctrine:migrations:migrate --no-interaction

auth-create-oauth-client:
	docker-compose run --rm auth-php-cli php bin/console trikoder:oauth2:create-client app ''

auth-oauth-keys:
	docker-compose run --rm auth-php-cli mkdir -p var/oauth
	docker-compose run --rm auth-php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm auth-php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm auth-php-cli chmod 644 var/oauth/private.key var/oauth/public.key

composer-install: auth-composer-install data-composer-install gateway-composer-install

auth-composer-install:
	docker-compose run --rm auth-php-cli composer install

data-composer-install:
	docker-compose run --rm data-php-cli composer install

gateway-composer-install:
	docker-compose run --rm gateway-php-cli composer install