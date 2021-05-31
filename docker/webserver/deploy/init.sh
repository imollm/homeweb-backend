#!/bin/bash

PROJECT_DIR=/var/www/html/homeweb-backend

cd "$PROJECT_DIR" && echo "Project directory ${PROJECT_DIR}"

if [ ! -d "${PROJECT_DIR}/vendor" ]; then
    echo "Installing dependencies" &&
    composer install
fi

cp .env.example .env && echo "Publish .env vars"
sed -i 's/MAIL_PASSWORD=/MAIL_PASSWORD=q4m12ZTAIeTf/g' .env && echo "Config email password"
php artisan config:clear && echo "Config clear"

echo "Migrating, seeding, generate keys and passport install" &&
php artisan migrate:fresh --seed &&
php artisan key:generate &&
php artisan passport:install

exit 0
