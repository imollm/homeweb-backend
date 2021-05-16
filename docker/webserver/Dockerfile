FROM php:8.0.3-apache-buster

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update --fix-missing && \
    apt-get upgrade -y && \
    apt-get --no-install-recommends install -y apt-utils && \
    rm -rf /var/lib/apt/lists/*

RUN apt-get -y update && \
    apt-get -y --no-install-recommends install nano wget \
dialog \
libsqlite3-dev \
libsqlite3-0 && \
    apt-get -y --no-install-recommends install default-mysql-client \
zlib1g-dev \
libzip-dev \
libicu-dev && \
    apt-get -y --no-install-recommends install --fix-missing apt-utils \
build-essential \
git \
curl \
libonig-dev && \
    apt-get -y --no-install-recommends install --fix-missing libcurl4 \
libcurl4-openssl-dev \
zip \
openssl && \
    rm -rf /var/lib/apt/lists/* && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug-3.0.0 && \
    docker-php-ext-enable xdebug

# Other PHP8 Extensions
RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-install pdo_sqlite && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install curl && \
    docker-php-ext-install tokenizer && \
    docker-php-ext-install zip && \
    docker-php-ext-install -j$(nproc) intl && \
    docker-php-ext-install mbstring && \
    docker-php-ext-install gettext && \
    docker-php-ext-install exif


# Install Freetype
RUN apt-get -y update && \
    apt-get --no-install-recommends install -y libfreetype6-dev \
libjpeg62-turbo-dev \
libpng-dev && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Enable apache modules
RUN a2enmod rewrite headers

# Cleanup
RUN rm -rf /usr/src/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Clone repository homeweb-backend
RUN cd /var/www/html && \
    git clone https://github.com/imollm/homeweb-backend

# Install dependencies
RUN cd /var/www/html/homeweb-backend && \
    /usr/local/bin/composer install

# Publish .env variables
#RUN cd /var/www/html/homeweb-backend && \
    #/usr/local/bin/php artisan config:cache

# Migrate database, init passport OAuth2 server and generate app keys
#RUN cd /var/www/html/homeweb-backend  && \
    #/usr/local/bin/php artisan migrate --seed
    #/usr/local/bin/php artisan key:generate && \
    #/usr/local/bin/php artisan passport:install


