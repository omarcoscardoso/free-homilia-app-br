#!/bin/sh

# Otimizações de cache em tempo de execução para produção (lê as variáveis do Cloud Run)
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

# Inicia o PHP-FPM em segundo plano
php-fpm -D

# Inicia o Nginx em primeiro plano
nginx -g "daemon off;"