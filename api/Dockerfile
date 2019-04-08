FROM php:7.2-fpm

# Copy composer.lock and composer.json
RUN mkdir -p /var/www/api.mobcamp.net
COPY composer.json composer.lock /var/www/api.mobcamp.net/

# Set working directory
WORKDIR /var/www/api.mobcamp.net

USER root
RUN apt-get update

# Install dependencies
RUN apt-get install -y \
    build-essential \
    mysql-client \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www || true
RUN useradd -u 1000 -ms /bin/bash -g www www || true

# Copy existing application directory contents
COPY . /var/www/api.mobcamp.net

# Copy existing application directory permissions
COPY --chown=www:www . /var/www/api.mobcamp.net

# SQL entrypoints
ADD start.sql /docker-entrypoint-initdb.d/start.sql

RUN docker-php-ext-install mbstring
RUN ln -s /usr/local/bin/php /usr/bin/php

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]