FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql \
    && a2enmod headers \
    && rm -rf /var/lib/apt/lists/*

ENV PORT=8080 \
    APP_ROOT=/var/www/html \
    SCHEMA_DIR=/opt/adota-pet/database \
    RUN_MIGRATIONS=1

COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache-entrypoint.sh /usr/local/bin/adota-pet-entrypoint

COPY index.php health.php /var/www/html/
COPY components /var/www/html/components
COPY css /var/www/html/css
COPY img /var/www/html/img
COPY js /var/www/html/js
COPY database /opt/adota-pet/database
COPY scripts /opt/adota-pet/scripts

RUN chmod +x /usr/local/bin/adota-pet-entrypoint \
    && chown -R www-data:www-data /var/www/html

EXPOSE 8080

ENTRYPOINT ["adota-pet-entrypoint"]
CMD ["apache2-foreground"]
