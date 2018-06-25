FROM debian:latest
MAINTAINER Vítězslav Dvořák <info@vitexsoftware.cz>

RUN apt update
RUN apt-get update && apt-get install -my wget gnupg

RUN wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key | apt-key add -
RUN echo deb http://v.s.cz/ stable main | tee /etc/apt/sources.list.d/vitexsoftware.list
RUN apt update


RUN apt-get update
RUN apt-get -y upgrade

RUN DEBIAN_FRONTEND=noninteractive apt-get -y install apache2 libapache2-mod-php php-mysql php-gd php-pear php-curl php-mbstring curl lynx-cur composer php-intl locales-all


RUN rm -f /var/www/html/index.html
COPY src/ /var/www/html/
RUN ln -s /var/www/html/ /var/www/src
COPY composer.json /var/www/composer.json

RUN composer install --no-dev --no-plugins --no-scripts  -d /var/www/

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

CMD ["/usr/sbin/apachectl","-DFOREGROUND"]
