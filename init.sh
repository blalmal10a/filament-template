#!/bin/bash

# Prompt for the folder name
read -p "Enter the folder name: " folder_name

# Convert folder name to snake_case for DB_DATABASE
db_database=$(echo "$folder_name" | tr '[:upper:]' '[:lower:]' | tr ' ' '_')

# Create the .env file with the required content
cat <<EOL > .env
APP_NAME="$folder_name"
APP_ENV=local
APP_KEY=base64:bbPTI5Bkep98BXQ+RUVEDaids6mJZAlDAAc8lgaE17c=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE="$db_database"
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\$APP_NAME"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="\$APP_NAME"

# SERVER_HOST=0.0.0.0
SERVER_PORT=
IMAGE_HOST=https://i.imgur.com
EOL

echo ".env file created successfully."

composer install
echo "Installation completed"
php artisan migrate
echo "Migration completed"
php artisan storage:link
php artisan key:generate
php artisan db:seed

echo "Seeding completed"

php artisan serve
