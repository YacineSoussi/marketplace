
build:
	docker-compose up -d --build

up:
	docker-compose up -d

down: 
	docker-compose down

stop:
	docker stop $$(docker ps -qa)

php:
	docker-compose exec php bash