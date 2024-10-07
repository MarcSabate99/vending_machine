build:
	docker-compose build

up:
	docker-compose up --detach

down:
	docker-compose down

restart:
	docker-compose down && docker-compose up --detach

install:
	docker-compose build
	docker-compose up --detach
	docker exec -it php-fpm composer install
	docker exec -it php-fpm mkdir -p db
	docker exec -it php-fpm mkdir -p tests/db

run:
	docker exec -it php-fpm php index.php

format:
	docker exec -it php-fpm vendor/bin/php-cs-fixer fix src
	docker exec -it php-fpm vendor/bin/php-cs-fixer fix tests

test-integration:
	docker exec -it php-fpm php vendor/bin/phpunit --testsuite integration

test-unit:
	docker exec -it php-fpm php vendor/bin/phpunit --testsuite unit
