FROM php:8.2-fpm as builder

RUN apt-get update && \
    apt-get install -y \
        nginx \
        git \
        unzip \
        libzip-dev \
        libonig-dev \
        libpq-dev \
        nodejs \
        npm \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql zip mbstring exif pcntl

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
        libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/www/html

COPY --from=builder /var/www/html /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/bootstrap/cache

# Copia as configurações do Nginx e o script de inicialização
COPY .docker/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
COPY .docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80
CMD ["/usr/local/bin/start.sh"]

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