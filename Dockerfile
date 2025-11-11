# Etapa base
FROM php:8.2-cli

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libz-dev \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Instala extensiones necesarias: grpc, protobuf
RUN pecl install grpc protobuf \
    && docker-php-ext-enable grpc protobuf

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copia archivos de composer
COPY composer.json /app/

# Instala dependencias del proyecto
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist

# Copia el resto de tu aplicación
COPY . /app

# Puerto por defecto
EXPOSE 8080

# Comando de inicio (usa PHP builtin server)
CMD
