FROM debian:trixie
LABEL maintainer="Vítězslav Dvořák <info@vitexsoftware.cz>"

ENV APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data \
    APACHE_LOG_DIR=/var/log/apache2 \
    APACHE_DOCUMENT_ROOT=/usr/share/flexplorer/ \
    DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get install -y wget libapache2-mod-php debconf-utils && \
    echo "deb http://repo.vitexsoftware.cz trixie main" | tee /etc/apt/sources.list.d/vitexsoftware.list && \
    wget -O /etc/apt/trusted.gpg.d/vitexsoftware.gpg http://repo.vitexsoftware.cz/KEY.gpg && \
    apt-get update && \
    apt-get install -y locales apache2 aptitude cron locales-all && \
    rm -rf /var/lib/apt/lists/* && \
    localedef -i cs_CZ -c -f UTF-8 -A /usr/share/locale/locale.alias cs_CZ.UTF-8
ENV LANG=cs_CZ.utf8

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    apt-get update && \
    echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections && \
    echo 'flexplorer flexplorer/FLEXIBEE_URL string https://demo.flexibee.eu' | debconf-set-selections && \
    echo 'flexplorer flexplorer/FLEXIBEE_LOGIN string winstrom' | debconf-set-selections && \
    echo 'flexplorer flexplorer/FLEXIBEE_PASSWORD string winstrom' | debconf-set-selections && \
    echo 'flexplorer flexplorer/FLEXIBEE_COMPANY string demo' | debconf-set-selections && \
    echo 'flexplorer flexplorer/BACKUP_DIRECTORY string /var/tmp' | debconf-set-selections && \
    printf '#!/bin/sh\nexit 101\n' > /usr/sbin/policy-rc.d && \
    chmod +x /usr/sbin/policy-rc.d && \
    apt-get install -y --no-install-recommends composer-debian composer && \
    apt-get install -y --no-install-recommends flexplorer || true && \
    rm /usr/sbin/policy-rc.d && \
    rm -rf /var/lib/apt/lists/*

EXPOSE 80
CMD [ "/usr/sbin/apache2ctl", "-D", "FOREGROUND" ]
