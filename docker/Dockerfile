FROM php:7.4-fpm

# Installing requirements
RUN apt-get update -y && apt-get install -y \
    openssl \
    zip \
    unzip \
    git \
    supervisor \
    libmcrypt-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    apt-transport-https \
    vim \
    gnupg \
    jpegoptim \
    optipng \
    pngquant \
    ffmpeg \
    gifsicle  \
    zlib1g-dev \
    libzip-dev \
    libonig-dev \
    cron

# Installing composer and docker extensions
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install -j$(nproc) gd pdo mbstring pdo_mysql exif pcntl zip bcmath
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

# Installing nodejs and npm
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash
RUN apt-get install nodejs npm -y

# Installing PCOV
RUN pecl install pcov && docker-php-ext-enable pcov

# Setting up and copy work directory
WORKDIR /var/www
COPY . /var/www
RUN chown -R www-data:www-data /var/www

# Setting up cron
COPY ./supervisor/horizon.conf /etc/supervisor/conf.d/
ADD ./cron/crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron
RUN touch /var/log/cron.log

# Setting up timezone
ENV TIME_ZONE=Europe/Istanbul
RUN ln -snf /usr/share/zoneinfo/$TIME_ZONE /etc/localtime && echo $TIME_ZONE > /etc/timezone

EXPOSE 9000

COPY ./docker-entrypoint.sh /

ENTRYPOINT ["/docker-entrypoint.sh"]

RUN ["chmod", "+x", "/docker-entrypoint.sh"]
