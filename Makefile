docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-build:
	docker-compose build

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

auth-oauth-keys:
	docker-compose run --rm auth-php-cli mkdir -p var/oauth
	docker-compose run --rm auth-php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm auth-php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm auth-php-cli chmod 644 var/oauth/private.key var/oauth/public.key