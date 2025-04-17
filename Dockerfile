FROM composer:2 as composer

# set working directory for Composer
WORKDIR /app

# copy only the files needed for depencency installation
COPY composer.json composer.lock ./

# install php dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts --no-progress

# php + apache image
FROM php:8.2-apache

# install required system tools and php extensions
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# enable apache mod_rewrite for Symfony routing
RUN a2enmod rewrite

# set working directory
WORKDIR /var/www/html

# copy full project
COPY . .
# copy installed vendor dependencies from composer stage
COPY --from=composer /app/vendor ./vendor

# replace default aapache config with custom one
COPY ./apache-config.conf /etc/apache2/sites-available/000-default.conf

# created required folders if they don't exist
RUN mkdir -p var/cache var/log

# set correct permissions to prevent permission issues
RUN chown -R www-data:www-data var
RUN chmod -R 775 var

# expose apache on port 80
EXPOSE 80

# start apache in the foreground
CMD ["apache2-foreground"]