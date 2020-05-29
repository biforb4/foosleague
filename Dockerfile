FROM php:7.4-apache

RUN apt-get update && apt-get install -y libpq-dev

RUN docker-php-ext-install pgsql pdo_pgsql

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker/php-apache/foosleague.conf /etc/apache2/sites-available/foosleague.conf
RUN a2dissite 000-default.conf && a2ensite foosleague.conf && a2enmod rewrite

COPY . /var/www/html
RUN rm -rf docker-dev && rm docker-compose.yml


