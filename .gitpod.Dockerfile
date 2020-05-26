# https://github.com/gitpod-io/workspace-images
# https://www.gitpod.io/blog/gitpodify/

# Option 1: All logic into a separated repository
# https://github.com/RedRudeBoy/gitpod-phalcon/tree/discoveryfy
FROM leninux/gitpod-phalcon:discoveryfy

# Option 2: All logic here
#FROM gitpod/workspace-full
#USER root
#ENV DEBIAN_FRONTEND noninteractive
#RUN apt-get upgrade -y && apt-get update -y \
    #&& apt-get install -y locales \
    #&& echo "en_US UTF-8" >> /etc/locale.gen \
    #&& echo "en_GB UTF-8" >> /etc/locale.gen \
    #&& locale-gen \
    # Install Mysql
    #&& docker-php-ext-install pdo_mysql \
    #&& docker-php-ext-install mysqli \
    #&& docker-php-ext-enable mysqli \
    # Install Redis
    #&& docker-php-ext-enable redis \
    # Install Zip
    #&& apt-get install -y zlib1g-dev libzip-dev unzip \
    #&& docker-php-ext-configure zip --with-libzip \
    #&& docker-php-ext-install zip \
    #&& docker-php-ext-enable zip \
    # Install GD
    #&& apt-get update -y \
    #&& apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    #&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    #&& docker-php-ext-install -j$(nproc) gd \
    # Enable apache mods
    #&& a2enmod rewrite \
    #&& a2enmod headers \
    #&& a2enmod ssl \
    #&& a2ensite default-ssl
    # Install Quasar
    # npm install -g @quasar/cli
    # npm install -g vue-cli && \
    # npm install -g @vue/cli@3.0.0 && \
    # npm install -g @vue/cli-init@3.0.0 && \
    # npm install -g quasar-cli@0.17.0 && \
    # npm install -g http-server@0.11.1

#USER gitpod
#WORKDIR ./api
#RUN set -eux; \
#    composer install --prefer-dist --no-scripts --no-progress --no-suggest; \
#    composer clear-cache;

# Install xdebug
# https://github.com/gitpod-io/Gitpod-PHP-Debug/blob/master/.gitpod.Dockerfile

#USER gitpod
#RUN sudo apt-get update -q \
#    && sudo apt-get install -y php-dev
#RUN wget http://xdebug.org/files/xdebug-2.9.1.tgz \
#    && tar -xvzf xdebug-2.9.1.tgz \
#    && cd xdebug-2.9.1 \
#    && phpize \
#    && ./configure \
#    && make \
#    && sudo cp modules/xdebug.so /usr/lib/php/20170718 \
#    && sudo bash -c "echo -e '\nzend_extension = /usr/lib/php/20170718/xdebug.so\n[XDebug]\nxdebug.remote_enable = 1\nxdebug.remote_autostart = 1\n' >> /etc/php/7.2/cli/php.ini"
