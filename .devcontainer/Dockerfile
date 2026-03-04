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
    sudo \
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

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create vscode user with same UID/GID as www-data to avoid permission issues
# UID 1000 is common on Linux and works well with Docker Desktop on Windows
ARG USER_UID=1000
ARG USER_GID=1000

# Modify www-data user to use UID/GID 1000
RUN groupmod -g ${USER_GID} www-data \
    && usermod -u ${USER_UID} -g ${USER_GID} www-data \
    && chown -R www-data:www-data /var/www

# Configure Apache to run as www-data
RUN sed -i 's/export APACHE_RUN_USER=.*/export APACHE_RUN_USER=www-data/' /etc/apache2/envvars \
    && sed -i 's/export APACHE_RUN_GROUP=.*/export APACHE_RUN_GROUP=www-data/' /etc/apache2/envvars

WORKDIR /var/www/html

# Copy setup script and entrypoint
COPY .devcontainer/setup-ogspy.sh /usr/local/bin/setup-ogspy.sh
COPY .devcontainer/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/setup-ogspy.sh /usr/local/bin/entrypoint.sh

# Ensure www-data owns the workdir and can write to it
RUN chown -R www-data:www-data /var/www/html

# Use our entrypoint to ensure setup runs before Apache starts
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
