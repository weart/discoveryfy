#Based on: https://hub.docker.com/r/kiksaus/kikdev/dockerfile

ARG PHP_VERSION=7.2
#ARG PHP_VERSION=7.4
ARG PHP_VARIANT=-apache
#ARG PHP_VARIANT=-alpine
FROM mileschou/phalcon:${PHP_VERSION}${PHP_VARIANT}

#https://hub.docker.com/r/phalconphp/bootstrap/builds
#FROM phalconphp/bootstrap:ubuntu-18.04

#https://hub.docker.com/r/phalconphp/base
#FROM phalconphp/base

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get upgrade -y && apt-get update -y \
    && apt-get install -y locales \
    && echo "en_US UTF-8" >> /etc/locale.gen \
    && echo "en_GB UTF-8" >> /etc/locale.gen \
    && locale-gen \

    # install curl, git, ping, vim & tzdata
    && apt-get update -y \
    && apt-get install -y \
        apt-utils \
        curl \
#        php-curl \
        git \
        iputils-ping \
        libpcre3-dev \
        vim \
        tzdata \

    # install mysql
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    # install postgres
#        libpq-dev \
##        libpq-dev postgresql-dev \
#    && docker-php-ext-install pdo pdo_pgsql \

    # install imagick
#    && apt-get install -y libmagickwand-dev --no-install-recommends \
#    && pecl install imagick \
#    && docker-php-ext-enable imagick \

    # install APCu
#    && pecl install APCu-5.1.8 \
#    && docker-php-ext-enable apcu \

    # install zip
    && apt-get update -y \
    && apt-get install -y zlib1g-dev libzip-dev unzip \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install zip \
    && docker-php-ext-enable zip \

    # install GD
    && apt-get update -y \
    && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \

    # Change this
    && openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/ssl-cert-snakeoil.key \
        -out /etc/ssl/certs/ssl-cert-snakeoil.pem -subj "/C=NL/ST=Holland/L=Alkmaar/O=Kiksaus/OU=Development/CN=kiksaus" \

    # Enable apache mods
    && a2enmod rewrite \
    && a2enmod headers \
    && a2enmod ssl \
    && a2ensite default-ssl \

    # install go
#    && apt-get update \
#    && apt-get install --no-install-recommends --assume-yes --quiet ca-certificates curl git \
#    && rm -rf /var/lib/apt/lists/* \

#    && curl -Lsf 'https://storage.googleapis.com/golang/go1.8.3.linux-amd64.tar.gz' | tar -C '/usr/local' -xvzf - \

    # install mhsendmail, needed for mailhog
#    && go get github.com/mailhog/mhsendmail \
#    && cp /root/go/bin/mhsendmail /usr/bin/mhsendmail \

    # errorlog config
    && echo "log_errors = on" >> /usr/local/etc/php/php.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini \
    && echo "error_log = /var/log/apache2/php_error.log" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_mode=req" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/php.ini \
#    && echo "xdebug.remote_host=192.168.65.1" >> /usr/local/etc/php/php.ini \
#    && echo "xdebug.remote_host=172.17.0.1" >> /usr/local/etc/php/php.ini
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/php.ini

# install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# install redis
#RUN pecl install redis && docker-php-ext-enable redis

# set servername
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
# change apache root folder
ENV APACHE_DOCUMENT_ROOT /var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# copy app
WORKDIR /var/www
COPY --chown=www-data:www-data ./app ./app
COPY --chown=www-data:www-data ./cli ./cli
COPY --chown=www-data:www-data ./library ./library
COPY --chown=www-data:www-data ./public ./public
COPY --chown=www-data:www-data ./vendor ./vendor
COPY --chown=www-data:www-data ./tests ./tests
COPY --chown=www-data:www-data composer.json composer.lock codeception.yml ./
COPY --chown=www-data:www-data .htaccess index.html .env phinx.php psalm.xml.dist ./
# Apache user (www-data) should be able to write into storage/logs folder
#RUN chown www-data:www-data /var/www
RUN usermod -u 1000 www-data \
 && groupmod -g 1000 www-data
#RUN ls -alh /var/www

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin:/var/www/vendor/bin"
RUN set -eux; \
    composer install --prefer-dist --no-scripts --no-progress --no-suggest; \
    composer clear-cache; \
    composer dump-autoload --classmap-authoritative --no-dev
#    ; \
#    composer run-script --no-dev post-install-cmd

# install phalcon devtools (in composer)
#ln -s ~/devtools/phalcon.php /usr/bin/phalcon
#chmod ugo+x /usr/bin/phalcon
# phalcon/devtools  v4.0.1  requires  psy/psysh (~0.9)
# psy/psysh suggests installing ext-pcntl (Enabling the PCNTL extension makes PsySH a lot happier :))

# upgrade machine & clean
RUN apt-get update && apt-get upgrade -y && apt-get autoclean && apt-get autoremove -y
#RUN rm -rf /var/cache/apt/* /var/lib/apt/lists/* /tmp/*

EXPOSE 80
EXPOSE 443
EXPOSE 9000
