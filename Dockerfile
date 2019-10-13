FROM iagapie/php:7.3-cli-alpine

MAINTAINER Igor Agapie <igoragapie@gmail.com>

RUN set -ex \
	&& groupmod -g 1000 www-data \
	&& usermod -u 1000 www-data \
	&& chown -Rf www-data:www-data /var/www \
	&& chown -Rf www-data:www-data /home/www-data \
	&& mv /usr/local/etc/php/conf.d/xdebug.iniOLD /usr/local/etc/php/conf.d/xdebug.ini

USER www-data

WORKDIR /var/www
