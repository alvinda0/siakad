# =========================
# Stage 1: Build Vite Assets
# =========================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

RUN npm run build

# =========================
# Stage 2: Laravel App
# =========================
FROM php:8.3-cli-alpine

# System dependencies
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    zip \
    oniguruma-dev \
    icu-dev \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev

# PHP Extensions
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    bcmath \
    intl \
    gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Copy source code
COPY . .

# Copy Vite build result
COPY --from=frontend /app/public/build ./public/build

# Permissions
RUN mkdir -p storage/logs bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

# Expose Render Port
EXPOSE 10000

# Start Laravel
CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}