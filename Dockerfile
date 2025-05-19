FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libxml2-dev libzip-dev unzip git \
    libicu-dev libxslt1-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd xml zip mysqli pdo pdo_mysql intl soap exif opcache

RUN echo "max_input_vars=5000" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/custom.ini

RUN printf "<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    DirectoryIndex index.php\n\
</Directory>\n" > /etc/apache2/conf-available/moodle.conf && \
    a2enconf moodle


RUN a2enmod rewrite