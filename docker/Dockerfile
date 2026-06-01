FROM php:8.2-apache

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Instalar dependencias del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache DocumentRoot para Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configurar AllowOverride para .htaccess de Laravel
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>' > /etc/apache2/conf-available/laravel.conf && a2enconf laravel

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Crear directorios de storage y fijar permisos base para www-data
# (el entrypoint los reaplica en cada inicio para cubrir volúmenes montados)
RUN mkdir -p storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copiar configuración PHP personalizada
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Copiar y configurar entrypoint para permisos automáticos
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer puerto 80
EXPOSE 80

# Usar entrypoint para configurar permisos antes de iniciar Apache
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
