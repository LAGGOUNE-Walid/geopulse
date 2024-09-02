FROM phpswoole/swoole:6.0-php8.2-alpine
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN apk update && apk add --no-cache supervisor
RUN apk add --no-cache libzip-dev
RUN apk add --no-cache msgpack-c
RUN chmod +x /usr/local/bin/install-php-extensions && sync
RUN apk add --no-cache linux-headers
RUN docker-php-ext-install pdo pdo_mysql zip pcntl exif sockets opcache 
RUN docker-php-ext-enable pdo pdo_mysql zip pcntl exif sockets opcache 
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apk add --update --virtual builds \
    libc-dev \
    yaml-dev \
    autoconf \
    re2c \
    make \
    gcc \
    g++ \
    gc
RUN pecl channel-update pecl.php.net
RUN pecl install msgpack
RUN docker-php-ext-enable msgpack
COPY src/ /var/www/html/
COPY supervisor/conf.d /etc/supervisor/conf.d
WORKDIR /var/www/html/
COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV DISABLE_DEFAULT_SERVER=1
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN apk add --no-cache bash
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/server.conf"]