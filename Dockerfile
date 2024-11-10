FROM php:7.4-apache

ARG app_port

# Change the Apache port to the provided argument
RUN sed -si 's/Listen 80/Listen '$app_port'/' /etc/apache2/ports.conf
RUN sed -si 's/VirtualHost .:80/VirtualHost *:'$app_port'/' /etc/apache2/sites-enabled/000-default.conf

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    libzip-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libicu-dev \
    libxml2-dev \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install zip && docker-php-ext-enable zip
RUN docker-php-ext-install gd && docker-php-ext-enable gd
RUN docker-php-ext-configure intl && docker-php-ext-install intl && docker-php-ext-enable intl
RUN docker-php-ext-install soap && docker-php-ext-enable soap
RUN docker-php-ext-install pgsql pdo_pgsql && docker-php-ext-enable pgsql pdo_pgsql
RUN docker-php-ext-install xmlrpc && docker-php-ext-enable xmlrpc
RUN docker-php-ext-install exif && docker-php-ext-enable exif
RUN docker-php-ext-install opcache

# Copy custom PHP config files
COPY ./php_conf/* /usr/local/etc/php/conf.d/

# Copy the Moodle files into the container
COPY ./moodle400 /var/www/html

# Set up the Moodle data directory
RUN mkdir /var/www/moodledata
RUN chown -R www-data:www-data /var/www/html /var/www/moodledata
RUN chmod -R 0777 /var/www/moodledata

# Expose the port
EXPOSE $app_port

# Start Apache service
CMD ["apache2-foreground"]