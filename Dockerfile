FROM debian:jessie
FROM vitexsoftware/easephpframework
FROM vitexsoftware/flexipeehp
COPY src/ /usr/share/flexplorer
COPY debian/conf/composer.json /usr/share/flexplorer/composer.json
