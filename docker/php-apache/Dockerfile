FROM php:7.4-apache

RUN apt-get update && apt-get install -y libpq-dev git libzip-dev
RUN pecl install xdebug

RUN docker-php-ext-install pgsql pdo_pgsql zip

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY foosleague.conf /etc/apache2/sites-available/foosleague.conf
RUN a2dissite 000-default.conf && a2ensite foosleague.conf && a2enmod rewrite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
