FROM php:7
MAINTAINER info@vitexsoftware

RUN wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key | apt-key add -
RUN echo deb http://v.s.cz/ stable main | sudo tee /etc/apt/sources.list.d/vitexsoftware.list 
RUN apt-get update && apt-get -y upgrade && \
  apt-get install -y zlib1g-dev git
RUN docker-php-ext-install zip mbstring
RUN apt install composer

FROM vitexsoftware/easephpframework
FROM vitexsoftware/flexipeehp
COPY src/ /usr/share/flexplorer
COPY debian/conf/composer.json /usr/share/flexplorer/composer.json


ENTRYPOINT ["/data/.docker/entrypoint.sh"]
CMD ["tests"]
