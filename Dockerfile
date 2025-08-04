# Usa a imagem oficial do PHP 8.2 com FPM
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx
RUN mkdir -p /var/www/html

COPY .docker/nginx.conf /etc/nginx/sites-available/default

RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

COPY index.php /var/www/html/index.php

WORKDIR /var/www/html
EXPOSE 80

# Inicia o PHP-FPM e o Nginx juntos
CMD service php8.2-fpm start && nginx -g "daemon off;"


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