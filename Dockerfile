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
COPY docker/php-production.ini /usr/local/etc/php/conf.d/zz-adota-pet-production.ini

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

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD php -r '$port = getenv("PORT") ?: "8080"; $body = @file_get_contents("http://127.0.0.1:" . $port . "/health.php"); exit($body !== false && str_contains($body, "\"status\":\"ok\"") ? 0 : 1);'

ENTRYPOINT ["adota-pet-entrypoint"]
CMD ["apache2-foreground"]
