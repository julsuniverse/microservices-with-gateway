docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-build:
	docker-compose build

migrations-diff:
	docker-compose run --rm data-php-cli php bin/console doctrine:migrations:diff

migrations-migrate:
	docker-compose run --rm data-php-cli php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker-compose run --rm data-php-cli php bin/console doctrine:fixtures:load --no-interaction