FROM php:8.2-apache

# Instalar extensões PHP necessárias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar arquivos da aplicação
COPY . /var/www/html/

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expor porta 80
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
