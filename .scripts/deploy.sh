#!/bin/bash
set -e

source .env

echo "Deployment started ..."

# Enter maintenance mode or return true
# if already is in maintenance mode
(php artisan down) || true

# Pull the latest version of the app
git pull origin production

# Install composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Clear the old cache
php artisan clear-compiled

# Recreate cache
php artisan optimize

# Compile npm assets
# npm run prod

# Run database migrations
php artisan migrate --force

WEBHOOK_URL="${APP_URL}/webhook"

echo "Webhook url: ${WEBHOOK_URL}"

curl "https://api.telegram.org/bot${BOT_TOKEN}/setWebhook?url=${WEBHOOK_URL}"

# Exit maintenance mode
php artisan up

echo "Deployment finished!"
