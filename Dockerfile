FROM php:8.3-cli

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev zip libxml2-dev nodejs npm \
    default-mysql-client \
    zbar-tools tesseract-ocr tesseract-ocr-por ghostscript \
    libmagickwand-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql xml zip gd \
    && docker-php-ext-enable gd \
    && pecl install imagick && docker-php-ext-enable imagick \
    && apt-get clean

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Script de inicialização
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
