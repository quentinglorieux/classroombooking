# Base image
FROM php:7.4-apache

# Set environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1

# Update and install required packages
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libldap2-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql intl mbstring zip gd ldap \
    && docker-php-ext-install mysqli && docker-php-ext-enable mysqli \
    && apt-get clean

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install Composer globally
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files to the working directory
COPY . /var/www/html/

# Set appropriate permissions
RUN mkdir -p /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage

    # Copy and overwrite the default Apache virtual host configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]