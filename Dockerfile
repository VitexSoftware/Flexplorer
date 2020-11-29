FROM debian:latest
MAINTAINER Vítězslav Dvořák <info@vitexsoftware.cz>

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_DOCUMENT_ROOT /usr/share/flexplorer/
env DEBIAN_FRONTEND=noninteractive

RUN apt update ; apt install -y wget libapache2-mod-php; echo "deb http://repo.vitexsoftware.cz buster main" | tee /etc/apt/sources.list.d/vitexsoftware.list ; wget -O /etc/apt/trusted.gpg.d/vitexsoftware.gpg http://repo.vitexsoftware.cz/keyring.gpg
RUN apt-get update && apt-get install -y locales apache2 aptitude  cron locales-all && rm -rf /var/lib/apt/lists/* \
    && localedef -i cs_CZ -c -f UTF-8 -A /usr/share/locale/locale.alias cs_CZ.UTF-8
ENV LANG cs_CZ.utf8

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN apt update

RUN aptitude -y install flexplorer

RUN a2ensite flexplorer

EXPOSE 80
CMD [ "/usr/sbin/apache2ctl", "-D", "FOREGROUND" ]
