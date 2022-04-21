
FROM php:5.6-fpm

# Update packages and install generic binaries
RUN \
    apt-get update && apt-get install -y \
        git \
        openssl \
        wget \
        unzip \
        zip

# Install typical PHP extensions
RUN \
    apt-get install -y \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-install \
        zip \
        intl \
        bcmath \
        mbstring \
        mysqli \
        pdo \
        pdo_mysql \
        opcache

# Install MySQL
RUN docker-php-ext-install mysql

# Install GD library
RUN \
    apt-get install -y \
        libpng-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
    && docker-php-ext-configure gd \
        --with-jpeg=/usr/include/ \
        --with-freetype-dir=/usr/include/ \
        --enable-gd-native-ttf \
    && docker-php-ext-install gd
