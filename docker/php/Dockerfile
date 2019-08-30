FROM php:7.2-fpm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y git libcurl3-dev zlib1g-dev

RUN docker-php-ext-install curl \
                           json \
                           zip \
                           opcache

RUN apt-get update  \
   && apt-get install  -y openssl libssl-dev libcurl4-openssl-dev \
   && pecl install mongodb \
   && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

RUN echo "date.timezone = Europe/Paris" >> /usr/local/etc/php/conf.d/app.ini
RUN echo "memory_limit = 1024M" >> /usr/local/etc/php/conf.d/app.ini


# nodejs
RUN apt-get install --yes gnupg
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash
RUN apt-get install --yes nodejs

#npm
RUN apt-get install --yes npm

# xdebug
RUN pecl install xdebug
RUN echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini


# rabbitMQ
RUN apt-get install --yes librabbitmq-dev
RUN pecl install amqp
RUN echo "extension=amqp.so" > /usr/local/etc/php/conf.d/amqp.ini

WORKDIR /srv/twity
