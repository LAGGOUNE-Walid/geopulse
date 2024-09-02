FROM phpswoole/swoole:6.0-php8.2
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN apt-get update && apt-get install nano -y && \
    apt-get install openssl -y && \
    apt-get install libssl-dev -y && \
    apt-get install wget -y && \
    apt-get install git -y && \
    apt-get install procps -y && \
    apt-get install libboost-all-dev -y && \
    apt-get install htop -y && \
    apt-get install libzip-dev -y
RUN apt-get install -y --no-install-recommends supervisor
RUN chmod +x /usr/local/bin/install-php-extensions && sync
RUN docker-php-ext-install pdo pdo_mysql zip pcntl pcntl
# ENV CFLAGS="$CFLAGS -D_GNU_SOURCE"
RUN install-php-extensions exif
RUN install-php-extensions sockets
RUN docker-php-ext-enable pdo pdo_mysql zip pcntl exif
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install opcache
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN apt-get update && apt-get install -y \
    libmsgpack-dev \
    && rm -rf /var/lib/apt/lists/*
RUN pecl install msgpack
RUN docker-php-ext-enable msgpack
COPY src/ /var/www/html/
COPY supervisor/conf.d /etc/supervisor/conf.d
WORKDIR /var/www/html/
COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV DISABLE_DEFAULT_SERVER=1
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
