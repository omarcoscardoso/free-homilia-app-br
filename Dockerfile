FROM php:8.2-apache 

# Configure PHP para Cloud Run.
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

# Instale as dependências do sistema operacional necessárias.
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    nodejs \
    npm \
    libonig-dev \   
    libxml2-dev \  
    libfreetype6-dev \ 
    libjpeg62-turbo-dev \
    libpng-dev \   
    libwebp-dev \  
    && rm -rf /var/lib/apt/lists/*

# Instale as extensões PHP comuns.
# NOTE: Para GD, use docker-php-ext-configure antes de docker-php-ext-install
RUN docker-php-ext-install -j "$(nproc)" pdo pdo_mysql zip opcache mbstring exif pcntl bcmath sockets

# Para a extensão GD, é comum precisar configurá-la primeiro:
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j "$(nproc)" gd

# Instale o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie o código da máquina host para o diretório de trabalho.
WORKDIR /var/www/html
COPY . ./

# Instale as dependências do Laravel via Composer
RUN composer install --no-dev --optimize-autoloader

# --- Início da seção para o Vite ---
RUN npm install
# Este comando criará o diretório public/build/ e o manifest.json
RUN npm run build
# --- Fim da seção para o Vite ---

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

# Garanta que o webserver tenha permissões de execução e escrita para o Laravel
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage
RUN chmod -R 775 /var/www/html/bootstrap/cache

# Limpar e otimizar caches do Laravel
RUN php artisan optimize:clear
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Use a variável de ambiente PORT nas configurações do Apache.
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/laravel.conf /etc/apache2/ports.conf

# Configure PHP para desenvolvimento.
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"