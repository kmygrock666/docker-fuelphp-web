FROM php:7.3-fpm

# redis install
ENV PHPREDIS_VERSION 5.0.2
RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz \
    && tar xfz /tmp/redis.tar.gz \
    && rm -r /tmp/redis.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv phpredis-$PHPREDIS_VERSION /usr/src/php/ext/redis \
    && docker-php-ext-install redis \
    && rm -rf /usr/src/php #如果这段不加构建的镜像将大100M 
# mysql install
RUN docker-php-ext-install pdo pdo_mysql mysqli 

COPY php.ini /usr/local/etc/php/conf.d/