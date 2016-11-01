FROM ubuntu:16.04

MAINTAINER Anthony Chomat <darknoon@darkcity.fr>

LABEL VERSION ="0.1"
LABEL DESCRIPTION="APACHE PHP OGSPY"

RUN apt-get -y update && apt-get install -y \
apache2 \
php \
libapache2-mod-php \
php-gd \
php-json \
php-sqlite3 \
php-mysql \
php-mcrypt \
mcrypt \
ssh \
mercurial \
git

RUN cd /var/www/html && hg clone ssh://hg@bitbucket.org/ogsteam/ogspy && hg update 3.3.2

EXPOSE 80 22