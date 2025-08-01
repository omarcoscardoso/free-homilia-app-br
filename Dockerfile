# Use the official PHP image.
# https://hub.docker.com/_/php
FROM php:8.4-apache

# Configure PHP for Cloud Run.
# Precompile PHP code with opcache.
RUN docker-php-ext-install -j "$(nproc)" opcache
RUN set -ex; \
    { \
      echo "; Cloud Run enforces memory & timeouts"; \
      echo "memory_limit = -1"; \
      echo "max_execution_time = 0"; \
      echo "; File upload at Cloud Run network limit"; \
      echo "upload_max_filesize = 32M"; \
      echo "post_max_size = 32M"; \
      echo "; Configure Opcache for Containers"; \
      echo "opcache.enable = On"; \
      echo "opcache.validate_timestamps = Off"; \
      echo "; Configure Opcache Memory (Application-specific)"; \
      echo "opcache.memory_consumption = 32"; \
    } > "$PHP_INI_DIR/conf.d/cloud-run.ini"

# Instale as dependências do sistema necessárias (ex: para extensões PHP como pdo_mysql, mbstring, etc. - adicione conforme necessário para seu Laravel)
# Exemplo para MySQL/MariaDB e outras extensões comuns:
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql zip opcache

# Instale o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy in custom code from the host machine.
WORKDIR /var/www/html
COPY . ./

# Instale as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Configure o Apache para o Laravel
RUN a2enmod rewrite
RUN echo "<VirtualHost *:80>\n" \
    "    DocumentRoot /var/www/html/public\n" \
    "    <Directory /var/www/html/public>\n" \
    "        AllowOverride All\n" \
    "        Require all granted\n" \
    "    </Directory>\n" \
    "    ErrorLog ${APACHE_LOG_DIR}/error.log\n" \
    "    CustomLog ${APACHE_LOG_DIR}/access.log combined\n" \
    "</VirtualHost>" > /etc/apache2/sites-available/laravel.conf

RUN a2ensite laravel.conf
RUN a2dissite 000-default.conf

# Ensure the webserver has permissions to execute index.php and for Laravel to write
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage
RUN chmod -R 775 /var/www/html/bootstrap/cache

# Use the PORT environment variable in Apache configuration files.
# https://cloud.google.com/run/docs/reference/container-contract#port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
# Ajuste: A linha acima deveria ser para o arquivo laravel.conf
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/laravel.conf /etc/apache2/ports.conf


# Configure PHP for development.
# Switch to the production php.ini for production operations.
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# https://github.com/docker-library/docs/blob/master/php/README.md#configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"