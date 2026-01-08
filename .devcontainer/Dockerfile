FROM php:8.5-apache

# Install system packages and PHP extensions at build time
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install mysqli pdo pdo_mysql zip mbstring xml bcmath gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Xdebug via PECL and enable it
RUN pecl install xdebug || pecl install xdebug-3.2.0 || true \
    && docker-php-ext-enable xdebug

# Configure Xdebug defaults (can be overridden with env vars)
RUN { \
    echo "xdebug.mode=debug,coverage"; \
    echo "xdebug.start_with_request=yes"; \
    echo "xdebug.client_host=${XDEBUG_CLIENT_HOST:-host.docker.internal}"; \
    echo "xdebug.client_port=${XDEBUG_CLIENT_PORT:-9003}"; \
} > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy setup script and entrypoint
COPY .devcontainer/setup-ogspy.sh /usr/local/bin/setup-ogspy.sh
COPY .devcontainer/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/setup-ogspy.sh /usr/local/bin/entrypoint.sh

# Use our entrypoint to ensure setup runs before Apache starts
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
