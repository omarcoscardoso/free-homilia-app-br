# Estágio 1: Builder
FROM composer:2.5 as builder
WORKDIR /app
COPY . .
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist --optimize-autoloader

# Estágio 2: App - A imagem final de produção
FROM php:8.2-fpm-alpine

# Instala o Nginx
RUN apk add --no-cache nginx

# Remove a configuração padrão do PHP-FPM e copia as nossas.
RUN rm /usr/local/etc/php-fpm.d/www.conf.default
COPY .docker/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY .docker/nginx.conf /etc/nginx/nginx.conf

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia toda a aplicação do estágio 'builder'
COPY --from=builder /app .

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html /var/run && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Otimiza o Laravel para produção
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expõe a porta que o Cloud Run espera
EXPOSE 8080

# Script de inicialização
COPY .docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Comando para iniciar os serviços
CMD ["/usr/local/bin/start.sh"]