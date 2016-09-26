FROM vitexsoftware/flexplorer
COPY src/ /usr/share/flexplorer
COPY debian/conf/composer.json /usr/share/flexplorer/composer.json
