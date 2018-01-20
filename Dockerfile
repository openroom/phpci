FROM ubuntu:rolling
RUN apt-get update
RUN apt-get update
RUN apt-get install -y libpq-dev zip locate tree unzip git php-cli php-dev php-pgsql php-pear php-xdebug composer php-mbstring php-xml
RUN echo "[Xdebug]" >> /etc/php/7.1/cli/php.ini
RUN echo zend_extension="/usr/lib/php/20160303/xdebug.so" >> /etc/php/7.1/cli/php.ini
COPY . /var/www/html/
WORKDIR /var/www/html/
RUN composer update --no-plugins --no-scripts
RUN chmod +x vendor/bin/phpunit
CMD ["vendor/bin/phpunit", "--debug --no-plugins --no-scripts --configuration phpunit_pgsql.xml --coverage-text"]