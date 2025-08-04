#!/bin/sh

# Inicia o PHP-FPM em primeiro plano
php-fpm -F &

# Inicia o Nginx em primeiro plano
nginx -g "daemon off;"