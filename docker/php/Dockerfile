FROM php:8.3-apache

# Dependências do sistema, ODBC Microsoft, extensões Laravel + sqlsrv/pdo_sqlsrv
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        gnupg2 curl ca-certificates unixodbc-dev libgssapi-krb5-2 \
        git unzip libzip-dev libicu-dev \
    && curl -sSL https://packages.microsoft.com/keys/microsoft.asc \
        | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl -sSL https://packages.microsoft.com/config/debian/12/prod.list \
        -o /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18 \
    && docker-php-ext-install intl bcmath pcntl zip \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Apache aponta para /public (front controller do Laravel)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
COPY app.conf /etc/apache2/conf-available/app.conf
RUN a2enconf app \
    && a2enmod rewrite headers

WORKDIR /var/www/html
