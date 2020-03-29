#!/bin/bash

# RUN git clone -b develop https://github.com/ogsteam/ogspy /app
# RUN git clone -b master https://github.com/ogsteam/mod-autoupdate /app/mod/autoupdate
# RUN chmod 755 /app/.docker/*.sh

# Change working directory
cd /app/.docker

# Change configuration files
mv /app/.docker/my.cnf /etc/mysql/conf.d/my.cnf
mv /app/.docker/supervisord-apache2.conf /etc/supervisor/conf.d/supervisord-apache2.conf
mv /app/.docker/supervisord-mysqld.conf /etc/supervisor/conf.d/supervisord-mysqld.conf

# Remove pre-installed database
rm -rf /var/lib/mysql/*

# config to enable .htaccess
mv /app/.docker/apache_default /etc/apache2/sites-available/000-default.conf
a2enmod rewrite

# Configure /app folder with sample app
chown -R www-data:www-data /app
mkdir -p /app && rm -fr /var/www/html && ln -s /app /var/www/html

# Configure OGSpy
rm -Rf /app/install
mv /app/.docker/id.php /app/parameters/id.php
mv /app/.docker/key.php /app/parameters/key.php

# Clean OGSpy
rm -Rf /app/.git