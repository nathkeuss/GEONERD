# utilise l'image PHP avec Apache
FROM php:8.2-apache

# installe les extensions PHP nécessaires pour Symfony
RUN docker-php-ext-install pdo pdo_mysql

# active le module mod_rewrite d'Apache
RUN a2enmod rewrite

# définit le dossier de travail
WORKDIR /var/www/html

# copie tout le projet AVANT d'installer Composer
COPY . .

COPY ./apache-config.conf /etc/apache2/sites-available/000-default.conf

# installe Composer et les dépendances Symfony
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-progress

# donne les permissions correctes
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html

# active Apache
CMD ["apache2-foreground"]
