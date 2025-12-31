FROM php:8.1-cli

WORKDIR /app

COPY backend /app/backend
COPY frontend /app/frontend
COPY database /app/database

RUN apt-get update && apt-get install -y \
    postgresql-client \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "backend"]
