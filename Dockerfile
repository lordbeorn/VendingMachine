FROM php:8.5-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    pkg-config \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure intl && \
    docker-php-ext-install -j$(nproc) \
        intl \
        pdo_mysql \
        zip

# Enable only what you need
RUN a2enmod rewrite

COPY docker/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY docker/apache/application.conf /etc/apache2/sites-available/application.conf
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2dissite 000-default.conf && \
    a2ensite application.conf

WORKDIR /var/www/html
