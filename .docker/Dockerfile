FROM ubuntu:latest

LABEL VERSION ="0.3"
LABEL DESCRIPTION="APACHE PHP OGSPY"
LABEL MAINTENER="darknoon@darkcity.fr"

# Install packages
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update --no-install-recommends && \
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

#Environment variables to configure php
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M

# Configure /app folder with sample app
COPY . /app
#RUN git clone -b develop https://github.com/ogsteam/ogspy /app
RUN git clone -b master https://github.com/ogsteam/mod-autoupdate /app/mod/autoupdate

# .SH executable
RUN chmod 755 /app/.docker/*.sh

# Configuration environment
WORKDIR /app/.docker/
RUN /app/.docker/configure.sh

# Add volumes for MySQL 
VOLUME  ["/etc/mysql", "/var/lib/mysql", "/app" ]

EXPOSE 80 3306
CMD ["/app/.docker/run.sh"]
