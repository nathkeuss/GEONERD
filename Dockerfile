# utilise l'image PHP avec Apache
FROM php:8.2-apache

# installe les extensions PHP nécessaires pour Symfony + outils système
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    && docker-php-ext-install pdo pdo_mysql

# active le module mod_rewrite d'Apache
RUN a2enmod rewrite

# installe Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# définit le dossier de travail
WORKDIR /var/www/html

# copie tout le projet
COPY . .

# copie la config Apache
COPY ./apache-config.conf /etc/apache2/sites-available/000-default.conf

# installe les dépendances Symfony (prod uniquement ici)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts --no-progress || true

# crée les dossiers var/cache et var/log au cas où ils n'existent pas
RUN mkdir -p var/cache var/log

# donne les bonnes permissions (évite les erreurs de cache/log)
RUN chown -R www-data:www-data var
RUN chmod -R 775 var

# démarre Apache
CMD ["apache2-foreground"]
