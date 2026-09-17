# Use the official PHP image
FROM php:8.2-cli

# Set the working directory
WORKDIR /app

# Copy your API code into the container
COPY . .

# Install OS dependencies required by PostgreSQL and Laravel
RUN apt-get update && apt-get install -y libpq-dev unzip
RUN docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Run migrations and start the Laravel server on the dynamic port
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT