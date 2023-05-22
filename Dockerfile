# Install dependencies
FROM composer as build
WORKDIR /build
COPY . .
RUN composer install -d /build/api/lib/composer

# # Serve index.php file with apache
# FROM php:7.4-apache
# FROM php:8.2-apache
# FROM php:8.1.18-apache
FROM php:8.0-apache
WORKDIR /app
COPY . .
COPY --from=build /build/api/lib/composer/vendor /app/api/lib/composer/vendor
RUN apt-get update && apt-get install -y \
    libxslt-dev \
    && docker-php-ext-configure xsl --with-xsl \
    && docker-php-ext-install -j$(nproc) xsl \
    && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /app

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

