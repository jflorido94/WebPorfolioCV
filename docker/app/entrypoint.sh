#!/bin/bash
set -e

echo "Starting Laravel portfolio initialization..."

# 1. Generate APP_KEY if missing
if [ "$(grep '^APP_KEY=' .env | cut -d'=' -f2 | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')" = "" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate
fi

# 2. Wait for database
echo "Waiting for database connection..."
RETRIES=30
DELAY=2
COUNT=0
while ! php artisan tinker << "TINKER" > /dev/null 2>&1
DB::connection()->getPdo();
TINKER
do
    COUNT=$((COUNT + 1))
    if [ $COUNT -ge $RETRIES ]; then
        echo "Database connection failed after ${RETRIES} attempts"
        exit 1
    fi
    echo "   Database not ready, retrying in ${DELAY}s... (${COUNT}/${RETRIES})"
    sleep $DELAY
done
echo "Database connected"

# 3. Run migrations
echo "Running database migrations..."
php artisan migrate --force

# 4. Check if admin user exists and seed if needed
echo "Checking if initial data needs seeding..."
ADMIN_COUNT=$(php artisan tinker << "TINKER" 2>/dev/null | sed -n 's/^> \([0-9]\+\)$/\1/p' | tail -1
echo(App\Models\User::where('is_admin', true)->count());
TINKER
)

if [ -z "$ADMIN_COUNT" ]; then
    ADMIN_COUNT=0
fi

if [ "$ADMIN_COUNT" = "0" ]; then
    echo "Seeding database with initial data..."
    php artisan db:seed --force
    echo "Database seeded successfully"
else
    echo "Initial data already exists, skipping seeders (found $ADMIN_COUNT admin user(s))"
fi

# 5. Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

# 6. Warm up caches
echo "Warming up application caches..."
php artisan config:cache
php artisan route:cache

echo "Laravel portfolio initialization complete!"
echo "Starting PHP-FPM..."

# Start PHP-FPM in foreground
exec php-fpm
