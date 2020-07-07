#Based on:
# https://github.com/MilesChou/docker-phalcon
# https://github.com/npulidom/alpine-nginx-php/blob/master/Dockerfile
# https://github.com/npulidom/alpine-phalcon/blob/master/Dockerfile
# https://github.com/phalcon/vokuro/blob/master/resources/Dockerfile
# https://hub.docker.com/r/kiksaus/kikdev/dockerfile

# Build arguments
ARG OS_TIMEZONE="Europe/Andorra"
ARG PHP_VERSION=7.4
ARG PHP_VARIANT=-fpm-alpine

# Based on official PHP image with Phalcon ( php:7.4-fpm-alpine + Phalcon + Psr )
FROM mileschou/phalcon:${PHP_VERSION}${PHP_VARIANT}

# OS alpine 3.11
#FROM nginx:1.19-alpine

# Alpine & nginx version
RUN cat /etc/os-release | grep PRETTY_NAME
# && nginx -v

LABEL maintainer="Leninux <leninux@fabri.cat>" \
      description="The backend for Discoveryfy"

# Environment vars
#ENV OS_TIMEZONE=$OS_TIMEZONE
#ENV FCGI_CONNECT=/var/run/php-fpm.sock \
#	PHP_FPM_PM=dynamic \
#	PHP_FPM_PM_MAX_CHILDREN=5 \
#	PHP_FPM_PM_START_SERVERS=2 \
#	PHP_FPM_PM_MIN_SPARE_SERVERS=1 \
#	PHP_FPM_PM_MAX_SPARE_SERVERS=3 \
#	PHP_FPM_PM_PROCESS_IDLE_TIMEOUT=10 \
#	PHP_FPM_PM_MAX_REQUESTS=0 \
##	PHP_FPM_ACCESS_FORMAT %R - %u %t \\\"%m %r\\\" %s

# Install docker help scripts
#COPY src/php/utils/docker/ /usr/local/bin/
#COPY src/php/utils/install-* /usr/local/bin/

ENV APP_ENV=production \
	APP_DEBUG=false \
	APP_URL=https://api.discoveryfy.fabri.cat \
	REDIS_HOST=discoveryfy_redis \
	MYSQL_HOST=discoveryfy_sql \
	MYSQL_DB=discoveryfy \
	MYSQL_USER=user \
	MYSQL_PASS=pass \
	INFLUXDB_HOST=discoveryfy_monitor \
	INFLUXDB_DB=discoveryfy \
	INFLUXDB_USER=user \
	INFLUXDB_PASS=pass \
	SEED_ROOT_USER=user \
	SEED_ROOT_PASS=pass \
	SEED_ROOT_MAIL=user@dom.ain

# Define /etc/localtime && /etc/timezone
#RUN apk update && apk add tzdata
#RUN cp /usr/share/zoneinfo/$OS_TIMEZONE /etc/localtime && echo $OS_TIMEZONE > /etc/timezone && date
# Remove other timezones
#RUN apk del tzdata

RUN apk update && apk add --no-cache \
#	wget \
	curl \
#	git \
#	php-gd \
#	php-fpm
# Redis
#	php-pdo \
#	php-redis \
# MySQL
#	php-mysql \
# ZIP
	libzip-dev \
#	php-zlib \
# Already installed / https://github.com/jbboehr/php-psr
#	php-phalcon \
#	php-psr \
#	&& rm -rf /var/cache/apk/*  \
# https://github.com/mlocati/docker-php-extension-installer
# ZIP & pcntl
	&& docker-php-ext-configure pcntl --enable-pcntl \
#	&& docker-php-ext-configure zip --with-libzip=/usr/include \
    && pecl install redis \
	&& docker-php-ext-install \
#	curl \
#	redis \
#	mysqli \
	pdo_mysql \
	zip \
	pcntl \
	&& docker-php-ext-enable \
    redis

# directory links (needed?)
#RUN ln -sf /etc/php7 /etc/php && \
#	ln -sf /usr/bin/php7 /usr/bin/php && \
#	ln -sf /usr/sbin/php-fpm7 /usr/bin/php-fpm && \
#	ln -sf /usr/lib/php7 /usr/lib/php

# Composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
#ENV COMPOSER_ALLOW_SUPERUSER=1
# Method 1: Download & copy the binary
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Method 2: Grab composer image
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#ENV PATH="${PATH}:/root/.composer/vendor/bin:/var/www/vendor/bin"
# Method 3: Only copy vendors
#FROM composer/composer:php7 as vendor
#WORKDIR /tmp/
#COPY composer.json composer.json
#COPY composer.lock composer.lock
#RUN composer install \
#    --ignore-platform-reqs \
#    --no-interaction \
#    --no-plugins \
#    --no-scripts \
#    --prefer-dist
#FROM php:7.2-apache-stretch
#COPY . /var/www/html
#COPY --from=vendor /tmp/vendor/ /var/www/html/vendor/

# install composer dependencies
#RUN set -eux; \
#    composer install --prefer-dist --no-scripts --no-progress --no-suggest; \
#    composer clear-cache; \
#    composer dump-autoload --classmap-authoritative --no-dev
##    ; \
##    composer run-script --no-dev post-install-cmd

# set www-data group (82 is the standard uid/gid for www-data in Alpine)
#RUN set -x; \
#	addgroup -g 82 -S www-data; \
#	adduser -u 82 -D -S -G www-data www-data && exit 0; exit 1

#RUN rm /etc/nginx/conf.d/default.conf

# Use configuration custom files
#COPY /storage/nginx/nginx.conf /etc/nginx/nginx.conf
#COPY /storage/nginx/vhost.conf /etc/nginx/sites-enabled/default
#COPY /storage/nginx/php-fpm.conf /etc/php7/php-fpm.d/www.conf

# Create a symlink to the recommended production configuration
# ref: https://github.com/docker-library/docs/tree/master/php#configuration
RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

#RUN mkdir -p /var/www/public
# /usr/share/nginx/html
COPY /api /var/www

WORKDIR /var/www
#ENTRYPOINT ["/var/www/storage/nginx/docker-nginx-entrypoint"]

#EXPOSE 80
