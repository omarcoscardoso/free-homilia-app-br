FROM php:8.2-fpm as builder

RUN apt-get update && \
    apt-get install -y \
    nginx \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Instala apenas as extensões essenciais para o funcionamento do Laravel e manipulação de arquivos/strings
RUN docker-php-ext-install zip mbstring exif pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# Segundo estágio: Imagem final, mais leve para produção.
FROM php:8.2-fpm

RUN apt-get update && \
    apt-get install -y \
    nginx \
    libzip-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip mbstring exif pcntl

RUN mkdir -p /var/www/html

COPY --from=builder /var/www/html /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/bootstrap/cache

COPY .docker/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
COPY .docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8080
CMD ["/usr/local/bin/start.sh"]
