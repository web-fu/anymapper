FROM php:8.1.0-fpm AS dev_phpfpm
WORKDIR /var/www/html

# update the system
RUN apt update \
    && apt autoremove -y

# add local user
RUN useradd -u 1000 local

# install Symfony 5.1.x requirements
RUN apt update \
    && apt install -y software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt install -y git libzip-dev zlib1g-dev unzip libpcre3 libpcre3-dev libxml2-dev libxslt-dev libpng-dev vim \
    && docker-php-ext-install pdo_mysql xsl zip gd intl

RUN apt-get install traceroute

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

#RUN apt update
#RUN apt install php-mbstring php-iconv

# opcache
RUN docker-php-ext-install opcache

# install xDebug
COPY ./phpfpm/xdebug/xdebug-3.1.4.tgz /usr/src/php/ext/xdebug.tgz
RUN tar -xf /usr/src/php/ext/xdebug.tgz -C /usr/src/php/ext/ && \
    rm /usr/src/php/ext/xdebug.tgz && \
    docker-php-ext-install xdebug-3.1.4
COPY ./phpfpm/xdebug/xdebug.ini /usr/local/etc/php/conf.d/

# load PHP configurations
COPY ./phpfpm/config/php.ini /usr/local/etc/php/
