# syntax=docker/dockerfile:1
FROM php:8.2-cli

# Crear directorio de la app
WORKDIR /app

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    unzip \
    git \
  && rm -rf /var/lib/apt/lists/*

# Instalar composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos de la app
COPY composer.json /app/
# (Si tienes lock, copy también composer.lock)
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist

COPY public /app/public

# Exponer puerto que usará Cloud Run (no obligatorio, pero ayuda)
EXPOSE 8080

# Arrancar servidor PHP en el puerto 8080
ENV PORT 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
