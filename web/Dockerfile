FROM ubuntu:18.04

ENV DEBIAN_FRONTEND=noninteractive

# update package list
RUN apt-get update

# install php
RUN apt-get -y install php7.2 php7.2-dev php-pear php7.2-curl php7.2-xml php7.2-cli php7.2-common php7.2-json php7.2-pgsql php7.2-mysql php7.2-mbstring php7.2-zip php7.2-soap libapache2-mod-php7.2 -y php7.2-gd

# install pre requisites
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/ubuntu/18.04/prod.list > /etc/apt/sources.list.d/msprod.list
RUN apt-get update
RUN ACCEPT_EULA=Y apt-get install -y mssql-tools unixodbc-dev
RUN echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile

# install driver sqlsrv
RUN pecl install sqlsrv pdo_sqlsrv
RUN echo extension=sqlsrv.so > /etc/php/7.2/mods-available/sqlsrv.ini
RUN echo extension=pdo_sqlsrv.so > /etc/php/7.2/mods-available/pdo_sqlsrv.ini
RUN phpenmod -v 7.2 -s ALL sqlsrv pdo_sqlsrv

# install locales
RUN apt-get install -y locales && echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && locale-gen

# misc
RUN apt-get install -y ssh

ADD php/php.ini /etc/php/7.2/apache2/php.ini
ADD php/php_cli.ini /etc/php/7.2/cli/php.ini

# Install git
RUN apt-get install git -y

RUN apt update && \
    apt install -y mysql-client

#Install google-chrome
RUN sh -c 'echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
RUN wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -
RUN apt update
RUN apt install -y google-chrome-stable
RUN apt install -y fonts-ipafont-gothic fonts-ipafont-mincho

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create User
ARG APP_USER=netcenter
ARG PUID=1000
ARG PGID=1000

ENV PUID ${PUID}
ENV PGID ${PGID}
ENV APP_USER ${APP_USER}

RUN groupadd --gid ${PGID} ${APP_USER} && \
  useradd --uid ${PUID} --gid ${APP_USER} --shell /bin/bash --create-home ${APP_USER} && \
  echo "${APP_USER} ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

# Set permission for APP_USER
RUN chown -R ${APP_USER}:${APP_USER} /var/www/html

ENV HOME /home/${APP_USER}

RUN service apache2 restart

WORKDIR /var/www/html/
