#!/bin/sh

# Inicia o PHP-FPM em primeiro plano
php-fpm -F &

# Aguarda um momento para o socket ser criado
sleep 1

# Ajusta as permissões do socket
chmod 777 /var/www/html/run/php-fpm.sock

# Inicia o Nginx em primeiro plano
nginx -g "daemon off;"