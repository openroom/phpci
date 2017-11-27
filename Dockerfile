FROM php:latest
RUN apt-get update -yqq
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql
RUN apt-get install git -yqq
RUN docker-php-ext-install pdo_pgsql
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN php --version
RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
COPY . /var/www/html/
WORKDIR /var/www/html/
RUN composer update
RUN chmod +x vendor/bin/phpunit
CMD ["vendor/bin/phpunit", "--configuration phpunit_pgsql.xml --coverage-text"]