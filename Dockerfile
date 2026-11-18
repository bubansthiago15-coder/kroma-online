FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libonig-dev \
    && docker-php-ext-install mbstring pdo_pgsql fileinfo \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html

RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && printf '\n<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' >> /etc/apache2/apache2.conf \
    && sed -ri 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf \
    && mkdir -p /var/www/html/storage/sessions /var/www/html/public/assets/images/uploads \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/public/assets/images/uploads

EXPOSE 10000
