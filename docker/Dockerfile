FROM ubuntu:latest

MAINTAINER Anthony Chomat <darknoon@darkcity.fr>

LABEL VERSION ="0.1"
LABEL DESCRIPTION="APACHE PHP OGSPY"

# Install packages
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
  apt-get -y install \
supervisor \
git \
mercurial \
apache2 \
libapache2-mod-php \
mariadb-server \
php-mysql \
pwgen \
php-mcrypt

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

ENV MYSQL_PASS="ogsteam"

# Add image configuration and scripts
ADD start-apache2.sh /start-apache2.sh
ADD start-mysqld.sh /start-mysqld.sh
ADD run.sh /run.sh
ADD my.cnf /etc/mysql/conf.d/my.cnf
ADD supervisord-apache2.conf /etc/supervisor/conf.d/supervisord-apache2.conf
ADD supervisord-mysqld.conf /etc/supervisor/conf.d/supervisord-mysqld.conf

# Remove pre-installed database
RUN rm -rf /var/lib/mysql/*

# Add MySQL utils
ADD create_mysql_admin_user.sh /create_mysql_admin_user.sh
RUN chmod 755 /*.sh

# config to enable .htaccess
ADD apache_default /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Configure /app folder with sample app
RUN hg clone https://bitbucket.org/ogsteam/ogspy /app
RUN hg clone https://bitbucket.org/ogsteam/mod-autoupdate /app/mod/autoupdate
RUN chown -R www-data:www-data /app
RUN mkdir -p /app && rm -fr /var/www/html && ln -s /app /var/www/html

#Environment variables to configure php
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M

# Add volumes for MySQL 
VOLUME  ["/etc/mysql", "/var/lib/mysql" ]

EXPOSE 80 3306
CMD ["/run.sh"]
