#!/usr/bin/env bash
wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key|sudo apt-key add -
echo deb http://v.s.cz/ stable main > /etc/apt/sources.list.d/vitexsoftware.list

apt-get update
apt-get install -y apache2
if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant /var/www
fi

export DEBIAN_FRONTEND="noninteractive"
apt-get update
apt-get install -y php7.0 php7.0-curl php-pear php7.0-intl libapache2-mod-php7.0 php7.0-zip
apt-get install -y flexplorer
cd /vagrant
composer update
