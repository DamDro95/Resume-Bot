up:
	docker compose up -d

down:
	docker compose down

install:
	docker compose run --rm app composer install
	@test -f .env || cp .env.example .env
	docker compose exec app php artisan key:generate

migrate:
	docker compose exec app php artisan migrate

fresh:
	docker compose run --rm app php artisan migrate:fresh --seed

shell:
	docker compose exec app bash

wait-for-db:
	@echo "Waiting for MySQL to be ready..."
	@until docker compose exec mysql mysqladmin ping -h localhost --silent 2>/dev/null; do \
		echo "MySQL not ready, retrying..."; \
		sleep 2; \
	done
	@echo "MySQL is ready!"

setup: up wait-for-db install migrate

logs:
	docker compose exec app tail -n 100 -f storage/logs/laravel.log

logs-nginx:
	docker compose logs -f nginx

npm-build:
	docker compose exec app npm run build

npm-install:
	docker compose exec app npm install

env-refresh:
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan cache:clear

queue:
	docker compose exec app php artisan queue:listen

fix-perms:
	docker compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

