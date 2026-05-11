# Menggunakan image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Install dependencies sistem dan ekstensi PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev zip unzip git \
    && docker-php-ext-install pdo pdo_pgsql

# Mengaktifkan mod_rewrite Apache (wajib untuk routing Laravel)
RUN a2enmod rewrite

# Mengubah root direktori Apache ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy semua file project ke dalam container
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Jalankan composer install
RUN composer install --no-dev --optimize-autoloader

# Berikan izin akses folder ke Apache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
