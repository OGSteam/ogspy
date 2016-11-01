FROM php:7.0-apache

MAINTAINER Anthony Chomat <darknoon@darkcity.fr>

LABEL VERSION ="0.1"
LABEL DESCRIPTION="APACHE PHP OGSPY"

COPY . /var/www/html/