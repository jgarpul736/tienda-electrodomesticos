# Imagen base
FROM php:8.2-apache
LABEL authors="Jesus Garcia Pulido"

RUN apt update
RUN pecl install xdebug && docker-php-ext-enable xdebug

# instalacion del driver PDO para mysql, falta añadir más adelante mysqli
RUN docker-php-ext-install pdo pdo_mysql


WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html

# Copiamos todos los ficheros de nuestro proyecto a la imagen
#COPY . /var/www/html

# Copiamos el archivo de configuración de Xdebug personalizado
COPY ./xdebug.ini /usr/local/etc/php/conf.d/docker-xdebug.ini

# Creación de la imagen con el comando
# docker build -t jesusgp17/php-apache8.2 .