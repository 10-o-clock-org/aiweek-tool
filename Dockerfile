FROM php:8.2-cli AS builder

# install composer to /composer.phar
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php

RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -

# install nodejs
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" > /etc/apt/sources.list.d/nodesource.list
RUN apt-get update && \
        DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs

# install php extensions
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -yq \
    zlib1g-dev libpng-dev libjpeg-dev libzip-dev \
    git \
    unzip

RUN docker-php-ext-install gd && \
	docker-php-ext-install zip

# copy app sources
COPY / /app
WORKDIR /app

RUN COMPOSER_ALLOW_SUPERUSER=1 php /composer.phar install

RUN npm -g install yarn
RUN yarn install
RUN yarn build

#------------------------------------------------------------------------------

FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT /app/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    sed -ri -e 's!Options Indexes FollowSymLinks!FallbackResource /index.php!' /etc/apache2/apache2.conf

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    echo 'date.timezone = "Europe/Berlin"' > $PHP_INI_DIR/conf.d/timezone.ini && \
    echo 'variables_order = "GPCSE"' > $PHP_INI_DIR/conf.d/variables-order.ini

COPY --from=builder /app /app
WORKDIR /app

RUN bin/console doctrine:database:create -n && \
    bin/console doctrine:schema:create -n && \
    bin/console doctrine:fixtures:load -n

ENV APP_ENV=prod
RUN bin/console cache:warmup -n && \
    chown -R www-data. var/ public/logos/
