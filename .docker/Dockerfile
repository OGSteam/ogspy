FROM ubuntu:latest

LABEL VERSION ="0.3"
LABEL DESCRIPTION="APACHE PHP OGSPY"
LABEL MAINTENER="darknoon@darkcity.fr"

# Install packages
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
apt-get -y install \
supervisor \
git \
apache2 \
libapache2-mod-php \
mariadb-server \
php-mysql \
php-xml \
php-zip \
pwgen \
vim \
&& apt-get clean \
&& rm -rf /var/lib/apt/lists/

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

ENV MYSQL_PASS="ogsteam"

# Add image configuration and scripts
COPY .docker/start-apache2.sh /root/start-apache2.sh
COPY .docker/start-mysqld.sh /root/start-mysqld.sh
COPY .docker/run.sh /root/run.sh
COPY .docker/my.cnf /etc/mysql/conf.d/my.cnf
COPY .docker/supervisord-apache2.conf /etc/supervisor/conf.d/supervisord-apache2.conf
COPY .docker/supervisord-mysqld.conf /etc/supervisor/conf.d/supervisord-mysqld.conf
COPY .docker/mysql-setup.sh /root/mysql-setup.sh
COPY .docker/ogspy_docker.sql /root/ogspy_docker.sql
# Remove pre-installed database
RUN rm -rf /var/lib/mysql/*

# Add MySQL utils
COPY .docker/create_mysql_admin_user.sh /root/create_mysql_admin_user.sh
RUN chmod 755 /root/*.sh

# config to enable .htaccess
COPY .docker/apache_default /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Configure /app folder with sample app
RUN git clone -b develop https://github.com/ogsteam/ogspy /app
RUN git clone -b master https://github.com/ogsteam/mod-autoupdate /app/mod/autoupdate
RUN chown -R www-data:www-data /app
RUN mkdir -p /app && rm -fr /var/www/html && ln -s /app /var/www/html

# Configure OGSpy
RUN rm -Rf /app/install
COPY .docker/id.php /app/parameters/id.php
COPY .docker/key.php /app/parameters/key.php


#Environment variables to configure php
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M

# Add volumes for MySQL 
VOLUME  ["/etc/mysql", "/var/lib/mysql", "/app" ]

EXPOSE 80 3306
CMD ["/root/run.sh"]
