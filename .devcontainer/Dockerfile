FROM php:8.4-apache

# Installer les extensions mysqli, pdo_mysql et zip, et activer rewrite
RUN apt-get update && apt-get install -y libzip-dev zip unzip git \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
