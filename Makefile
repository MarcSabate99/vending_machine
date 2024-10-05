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

