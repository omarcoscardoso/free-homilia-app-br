
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

# Copy in custom code from the host machine.
WORKDIR /var/www/html
COPY . ./

# Ensure the webserver has permissions to execute index.php
RUN chown -R www-data:www-data /var/www/html

# Use the PORT environment variable in Apache configuration files.
# https://cloud.google.com/run/docs/reference/container-contract#port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Configure PHP for development.
# Switch to the production php.ini for production operations.
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# https://github.com/docker-library/docs/blob/master/php/README.md#configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# # Estágio 1: Builder
# FROM composer:2.5 as builder
# WORKDIR /app
# COPY . .
# RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist --optimize-autoloader

# # Estágio 2: App - A imagem final de produção
# FROM php:8.2-fpm-alpine

# # Instala o Nginx e as dependências
# RUN apk add --no-cache \
#         nginx \
#         libzip-dev \
#         libxml2-dev \
#         zlib-dev \
#         oniguruma-dev \
#     && docker-php-ext-install \
#         bcmath \
#         ctype \
#         fileinfo \
#         mbstring \
#         xml \
#         zip

# # Remove a configuração padrão do PHP-FPM e copia as nossas.
# COPY .docker/www.conf /usr/local/etc/php-fpm.d/www.conf
# COPY .docker/nginx.conf /etc/nginx/nginx.conf

# # Define o diretório de trabalho
# WORKDIR /var/www/html

# # Copia toda a aplicação do estágio 'builder'
# COPY --from=builder /app .

# # Ajusta permissões
# RUN mkdir -p /var/www/html/run && \
#     chown -R www-data:www-data /var/www/html && \
#     chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/run

# # Otimiza o Laravel para produção
# RUN php artisan config:cache && \
#     php artisan route:cache && \
#     php artisan view:cache

# # Expõe a porta que o Cloud Run espera
# EXPOSE 8080

# # Script de inicialização
# COPY .docker/start.sh /usr/local/bin/start.sh
# RUN chmod +x /usr/local/bin/start.sh

# # Comando para iniciar os serviços
# CMD ["/usr/local/bin/start.sh"]


##########################################################################
# FROM php:8.4-apache 

# # Configure PHP para Cloud Run.
# RUN docker-php-ext-install -j "$(nproc)" opcache
# RUN set -ex; \
#     { \
#       echo "; Cloud Run enforces memory & timeouts"; \
#       echo "memory_limit = -1"; \
#       echo "max_execution_time = 0"; \
#       echo "; File upload at Cloud Run network limit"; \
#       echo "upload_max_filesize = 32M"; \
#       echo "post_max_size = 32M"; \
#       echo "; Configure Opcache for Containers"; \
#       echo "opcache.enable = On"; \
#       echo "opcache.validate_timestamps = Off"; \
#       echo "; Configure Opcache Memory (Application-specific)"; \
#       echo "opcache.memory_consumption = 32"; \
#     } > "$PHP_INI_DIR/conf.d/cloud-run.ini"

# # Instale as dependências do sistema operacional necessárias.
# RUN apt-get update && apt-get install -y \
#     libpq-dev \
#     libzip-dev \
#     unzip \
#     git \
#     nodejs \
#     npm \
#     libonig-dev \   
#     libxml2-dev \  
#     libfreetype6-dev \ 
#     libjpeg62-turbo-dev \
#     libpng-dev \   
#     libwebp-dev \  
#     && rm -rf /var/lib/apt/lists/*

# # Instale as extensões PHP comuns.
# # NOTE: Para GD, use docker-php-ext-configure antes de docker-php-ext-install
# RUN docker-php-ext-install -j "$(nproc)" pdo pdo_mysql zip opcache mbstring exif pcntl bcmath sockets

# # Para a extensão GD, é comum precisar configurá-la primeiro:
# RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
# RUN docker-php-ext-install -j "$(nproc)" gd

# # Instale o Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Copie o código da máquina host para o diretório de trabalho.
# WORKDIR /var/www/html
# COPY . ./

# # Instale as dependências do Laravel via Composer
# RUN composer install --no-dev --optimize-autoloader

# # --- Início da seção para o Vite ---
# RUN npm install
# # Este comando criará o diretório public/build/ e o manifest.json
# RUN npm run build
# # --- Fim da seção para o Vite ---

# # Configure o Apache para o Laravel
# RUN a2enmod rewrite
# RUN echo "<VirtualHost *:80>\n" \
#     "    DocumentRoot /var/www/html/public\n" \
#     "    <Directory /var/www/html/public>\n" \
#     "        AllowOverride All\n" \
#     "        Require all granted\n" \
#     "    </Directory>\n" \
#     "    ErrorLog ${APACHE_LOG_DIR}/error.log\n" \
#     "    CustomLog ${APACHE_LOG_DIR}/access.log combined\n" \
#     "</VirtualHost>" > /etc/apache2/sites-available/laravel.conf

# RUN a2ensite laravel.conf
# RUN a2dissite 000-default.conf

# # Garanta que o webserver tenha permissões de execução e escrita para o Laravel
# RUN chown -R www-data:www-data /var/www/html
# RUN chmod -R 777 /var/www/html/storage
# RUN chmod -R 777 /var/www/html/bootstrap/cache

# # Limpar e otimizar caches do Laravel
# RUN php artisan optimize:clear
# RUN php artisan config:cache
# RUN php artisan route:cache
# RUN php artisan view:cache

# # Use a variável de ambiente PORT nas configurações do Apache.
# RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/laravel.conf /etc/apache2/ports.conf

# Configure PHP para desenvolvimento.
# RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

##########################################################################


## TESTE LOCAL DA IMAGEM
#
# docker build -t homilia-app:latest .
#
# docker run -p 8080:80 \
#            --env APP_NAME="HomilIA" \
#            --env APP_ENV="production" \
#            --env APP_KEY="base64:CfqGMuEtBoOibR6hJvcCZ9IG+Fq2F/0wZlqMhzGeqFc=" \
#            --env APP_DEBUG="true" \
#            --env APP_URL="http://localhost:8080" \
#            --env VITE_APP_NAME="HomilIA" \
#            --env GEMINI_API_KEY="AIzaSyDGMdE1LQlwo1DifL1Kv7v1jmqPJnFA29YS" \
#            --env SESSION_DRIVER="cookie" \
#            --env CACHE_DRIVER="file" \
#            --env BCRYPT_ROUNDS="12" \
#            --env PORT="80" \
#            homilia-app:latest

# APP_NAME=HomilIA
# APP_ENV=local
# APP_KEY=base64:JqSEd92CjBuhmksKTgD9HrhF9IXnfojLzushwr9eAcw=
# APP_DEBUG=true
# APP_URL=https://homilia-app-1042749847534.us-central1.run.app
# APP_LOCALE=pt_BR
# APP_MAINTENANCE_DRIVER=file
# CACHE_DRIVER = file
# BCRYPT_ROUNDS=12
# SESSION_DRIVER=cookie
# GEMINI_API_KEY=AIzaSyDGMdE1LQlwo1DifL1Kv7v1jmqPJnFA29Y


# docker tag homilia-app:latest gcr.io/iprviamao-169920/homilia-app:latest

# Em seguida, faça o push para o registro
# docker push gcr.io/iprviamao-169920/homilia-app:latest