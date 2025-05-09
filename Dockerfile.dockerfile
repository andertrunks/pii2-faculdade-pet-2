# Imagem base PHP 8.3 já com Apache
FROM php:8.3-apache

# Instale pdo_mysql + habilite mod_rewrite (útil para futuras rotas "bonitas")
RUN docker-php-ext-install pdo pdo_mysql \
  && a2enmod rewrite

# Copie o projeto para a pasta pública do Apache
# (colocamos tudo em /var/www/html/; se quiser pode mover só /components, /css etc.)
COPY . /var/www/html/

# Apache escuta na porta 80 por padrão – isso FUNCIONA no Render,
# mas deixar explícito ajuda em testes locais
ENV PORT=8080
EXPOSE ${PORT}

# Start do Apache
CMD ["apache2-foreground"]
