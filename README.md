# Dockerize an Existing Drupal Project
Drupal 8 is not new now and most developers have started using Docker for local development. Most of them are familiar with Docker, however, few developers still struggle to setup Docker locally.
There are tons of documents available on the Internet which describe how to set up a local Docker + Drupal development environment. They are really helpful but most of them come with their own custom images.
Considering folks who are new to Docker find this documentation complicated sometimes because to run Docker locally requires a lot of configurations.
On top of that, there are multiple docker images available on Web, and developers who want to use docker in existing projects can't finalize
what is the right Docker image for their project.

# Problem Statement
If an existing project development is already happening without Docker (maybe using XAMPP, WAMP, Acquia Dev Desktop or Vagrant etc.)
then developers don't want to spend much time on setting up it on Docker. They want to minimize the setup time and to do that they look for any relevant Docker image. Sometimes they get it easily but most of the time they find that there are some extra modules/configurations/components/libraries that are not required in their project. Hence, either they don't setup it on Docker or look for plain PHP, Apache and MySql image that they can update as per their requirements.

# Solution
1. Create separate containers for PHP, MySQL, and Apache. To avoid any unnecessary complexities, libraries that may happen if we use irrelevant docker images.  
2. Manage version for all the containers separately.
3. Whatever the libraries needed, just add them in the individual docker file.
4. Setup the local repository, error logs etc. path in the docker file itself.

# Let's start and follow the below steps to maintain an existing project using Docker.

## Prerequisites

1. [Docker](https://docs.docker.com/engine/installation/)
2. [Docker-Compose](https://docs.docker.com/compose/install/)
3. [Understanding of Composer](https://getcomposer.org/doc/00-intro.md/)


## Quick Setup
1. Clone this repository and run the below command:
```
docker-compose up -d
```
2. If it runs successfully, you will see below message:
```
Creating php   ... done
Creating mysql ... done
Creating apache ... done
```
3. To see docker images run below command:
```
docker ps
```
You will see individual containers for PHP, MySql and Apache as shown below:

| CONTAINER ID | IMAGE | COMMAND | CREATED | STATUS | PORTS | NAMES |
| -------------| ------ | ------ | ------- | ------ | ----- | -----|
| 09f51b5f330b | dockerize-existing-drupal-project_apache | "httpd-foreground" | 32 seconds ago | Up 30 seconds | 0.0.0.0:80->80/tcp | apache |
| 8294e1800058 | dockerize-existing-drupal-project_php | "docker-php-entrypoi…" | 33 seconds ago | Up 31 seconds | 9000/tcp | php |
| d8b2cbac5695 | mysql:8.0.0 | "docker-entrypoint.s…" | 33 seconds ago | Up 31 seconds | 0.0.0.0:3306->3306/tcp | mysql |

4. Now hit the http://localhost:80 in browser to ensure setup is successful. Along with the below output, you will also see phpinfo() output.
```
Congratulations!! Docker setup connection is successful.
Checking MySQL integration with php.
MySql connection is successful!
```
5. With the current setup you will get below versions but you can change it anytime in `.env` file.
```
PHP Version = 7.3.11
APACHE_VERSION = 2.4.41
MYSQL_VERSION = 8.0.0
```
6. Now put your codebase inside `docroot` folder that is pointed to `/var/www/html` in the PHP container. You can also use [Drupal Composer Project](https://github.com/drupal-composer/drupal-project) to create a Drupal installation from scratch.

You can jump to command line prompt of PHP container using below docker command:
```
docker exec -it 8294e1800058 /bin/sh
```
`8294e1800058` is PHP container id as shown in the above table. Please note this container id can different on your machine.

7. Since the Drush and composer are already part of PHP docker image, you can simply import the database using drush.

8. This way the basic setup is done and you can change the composer file accordingly. In case any additional PHP library is needed, update the PHP docker file and rebuild the PHP Docker image.

## Detailed Explanation
1. Install [docker](https://www.docker.com/) on your machine. You may verify the installation by running below command on your terminal.
```
docker -D info
```

2. Fork this repository to your machine where you want to manage your codebase.
3. Spin up the docker now using below command to create PHP, Apache and MySql containers.
```
docker-compose up -d
```
Adding the `-d` means it will start in detached mode, allowing you to continue to use that terminal window while the containers run in the background.

Once you run this command, it will create all the containers and install all the relevant libraries defined in docker files. I will talk about individual docker files in detail separately. If it runs successfully, you will see the below messages.

```
Creating mysql ... done
Creating php   ... done
Creating apache ... done
```
4. Now take a look at PHP, Apache and MySql containers using below command:
```
docker ps
```
| CONTAINER ID | IMAGE | COMMAND | CREATED | STATUS | PORTS | NAMES |
| -------------| ------ | ------ | ------- | ------ | ----- | -----|
| 09f51b5f330b | dockerize-existing-drupal-project_apache | "httpd-foreground" | 32 seconds ago | Up 30 seconds | 0.0.0.0:80->80/tcp | apache |
| 8294e1800058 | dockerize-existing-drupal-project_php | "docker-php-entrypoi…" | 33 seconds ago | Up 31 seconds | 9000/tcp | php |
| d8b2cbac5695 | mysql:8.0.0 | "docker-entrypoint.s…" | 33 seconds ago | Up 31 seconds | 0.0.0.0:3306->3306/tcp | mysql |

5. Now jump into the PHP container using the `docker exec` command.
```
docker exec -it 8294e1800058 /bin/sh
```
Since you will be using the `root` user inside the container, so you should have permissions to do anything. The code in your git repository can be found at `/var/www/html` in the container. To ensure container mounted is successful, run the below command.
```
/var/www/html # ls
```
If it shows you `index.php` that means it mounts to your local repository i.e. `docroot`.

6. Now hit the http://localhost:80 in browser to ensure setup is successful. Along with the below output, you will also see `phpinfo()` output.
```
Congratulations!! Docker setup connection is successful.
Checking MySQL integration with php.
MySql connection is successful!
```

7. Now the docker setup is done, you can put your local codebase inside the `docroot`. Now it's up to you whether you want to run multiple drupal instances within this repository or only one. If you want to manage multiple repositories then keep the project folder inside the `docroot` repository. For example, in our case, it's `drupal8` repository.

```
.
├── LICENSE
├── README.md
├── apache
│   ├── Dockerfile
│   └── local.apache.conf
├── docker-compose.yml
├── docroot
│   └── drupal8
|         ├── config
│         ├── drush
│         ├── scripts
│         ├── vendor
│         └── web
|          ├── core
│          ├── modules
│          ├── profiles
│          ├── sites
│          └── themes
└── php
    └── Dockerfile
```

Now this project Drupal's docroot should be located at `/var/www/html/drupal8/web`.

8. Now update your project's database credentials in `settings.php` or `settings.local.php` wherever you are managing. As we are already in PHP container `/var/www/html/drupal8/web`, hence we can import the database using Drush:

```
drush sql-cli < ~/my-sql-dump-file-name.sql
```
Here `my-sql-dump-file-name.sql` is your mysql database backup file. It can different in your case.

9. Now access your drupal project in browser. For example, in our case it would be below URL:
```
http://localhost/drupal8/web
```
We may create virtual host entry for above URL. With some browsers and operating systems any path ending in `localhost` will work automatically, otherwise you may need update your hosts file so your browser will know it's a local url.

10. Congratulations! Now you have a full local Drupal Hosting environment.

## Issues and Resolutions

**Issue:** While importing or creating the database, you may face this error `ERROR 1044 (42000): Access denied for user 'drupal'@'%'`

**Resolution:** You should connect with admin credentials. In our case, the user name is root and the password is admin.

**Issue:** `ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/ru`

**Resolution:** Make sure correct host name is there and database access settings in your `settings.php` corresponds to values in `.env` file. In our case, the host name is mysql.

```
$databases['default']['default'] = array (
  'database' => 'drupal',
  'username' => 'drupal',
  'password' => 'drupal123',
  'prefix' => '',
  'host' => 'mysql',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);
```
**Issue:** How to install & use PhpMyAdmin with Docker?

**Resolution:** https://github.com/erpushpinderrana/dockerize-existing-drupal-project/issues/4

## Directory Structure
Once you clone this respository, it comes with default index.php in docroot folder. Inside this docroot, you can replace it with your existing project codebase. For example, I have added drupal8 codebase that looks like below:
```
.
├── LICENSE
├── README.md
├── apache
│   ├── Dockerfile
│   └── local.apache.conf
├── docker-compose.yml
├── docroot
│   └── drupal8
|         ├── config
│         ├── drush
│         ├── scripts
│         ├── vendor
│         └── web
|          ├── core
│          ├── modules
│          ├── profiles
│          ├── sites
│          └── themes
└── php
    └── Dockerfile
```
## Docker Files Explanation
Docker files define a sets of services which make up an entire application. It allows you to define the dependencies for those services, networks and volumes etc.

#### docker-compose.yml
```
version: "3.2"
services:
  php:
    build:
      context: './php/'
      args:
       PHP_VERSION: ${PHP_VERSION}
    networks:
      - backend
    volumes:
      - ${PROJECT_ROOT}/:/var/www/html/
    container_name: php
  apache:
    build:
      context: './apache/'
      args:
       APACHE_VERSION: ${APACHE_VERSION}
    depends_on:
      - php
      - mysql
    networks:
      - frontend
      - backend
    ports:
      - "80:80"
    volumes:
      - ${PROJECT_ROOT}/:/var/www/html/
    container_name: apache
  mysql:
    image: mysql:${MYSQL_VERSION:-latest}
    restart: always
    ports:
      - "3306:3306"
    volumes:
            - data:/var/lib/mysql
    networks:
      - backend
    environment:
      MYSQL_ROOT_PASSWORD: "${DB_ROOT_PASSWORD}"
      MYSQL_DATABASE: "${DB_NAME}"
      MYSQL_USER: "${DB_USERNAME}"
      MYSQL_PASSWORD: "${DB_PASSWORD}"
    container_name: mysql
networks:
  frontend:
  backend:
volumes:
    data:

```
Here version is Docker version. It has php, apache and mysql services. To avoid any custom image for PHP and apache, we are using context here to define the PHP & Apache dockerfile separately. In case of Mysql, we are directly downloading the MySql image because MySql container doesn't require any special library or configuration. You may create a separate Dockerfile for MySql if it's required in your case.  

#### Apache Dockerfile
```
ARG APACHE_VERSION=""
FROM httpd:${APACHE_VERSION:+${APACHE_VERSION}-}alpine

RUN apk update; \
    apk upgrade;

# Copy apache vhost file to proxy php requests to php-fpm container
COPY local.apache.conf /usr/local/apache2/conf/local.apache.conf
RUN echo "Include /usr/local/apache2/conf/local.apache.conf" \
    >> /usr/local/apache2/conf/httpd.conf
```
It downloads the alpine-based apache image for the version defined in `.env` file. In our case, it's `2.4.41`. You can change it as per your requirements.

#### PHP Dockerfile

```
ARG PHP_VERSION=""
FROM php:${PHP_VERSION:+${PHP_VERSION}-}fpm-alpine

RUN apk update; \
    apk upgrade;

# Install gd library extension
RUN apk add libpng libpng-dev libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev gd && docker-php-ext-install gd    

# Install MySql
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    ln -s /root/.composer/vendor/bin/drush /usr/local/bin/drush

# Install Drush
RUN composer global require drush/drush && \
    composer global update

# PHP packages
RUN apk add --update \
        libressl \
        ca-certificates \
        openssh-client \
        rsync \
        git \
        curl \
        wget \
        gzip \
        tar \
        patch \
        perl \
        pcre \
        imap \
        imagemagick \
        mariadb-client \
        build-base \
        autoconf \
        libtool \
        php7-dev \
        pcre-dev \
        imagemagick-dev \
        php7 \
        php7-fpm \
        php7-opcache \
        php7-session \
        php7-dom \
        php7-xml \
        php7-xmlreader \
        php7-ctype \
        php7-ftp \
        php7-gd \
        php7-json \
        php7-posix \
        php7-curl \
        php7-pdo \
        php7-pdo_mysql \
        php7-sockets \
        php7-zlib \
        php7-mcrypt \
        php7-mysqli \
        php7-sqlite3 \
        php7-bz2 \
        php7-phar \
        php7-openssl \
        php7-posix \
        php7-zip \
        php7-calendar \
        php7-iconv \
        php7-imap \
        php7-soap \
        php7-dev \
        php7-pear \
        php7-redis \
        php7-mbstring \
        php7-xdebug \
        php7-exif \
        php7-xsl \
        php7-ldap \
        php7-bcmath \
        php7-memcached \
        php7-oauth \
        php7-apcu
```
It downloads the php image for the version defined in `.env` file. In our case, it's `7.3`. You can change it as per your requirements. Also, I have added all the minimal PHP libraries along with Composer and Drush to ensure we don't face any issue with core Drupal 8. You may add/update/delete any library in PHP dockerfile as per your requirements.

## Important Docker commands
 * ``docker-compose up`` Start all the containers.
 * ``docker-compose down`` Stop all the containers.
 * ``docker system prune -a`` Delete all the docker images.
 * ``docker ps`` See all active containers.
 * ``docker container inspect`` Display detailed information on one or more containers
 * ``docker stop $(docker ps -a -q)`` To stop all of Docker containers 
 * ``docker rm $(docker ps -a -q)``  To remove all of Docker containers
 * ``docker network prune`` To remove all networks not used by at least one container.
 * ``docker inspect <image_name>`` To inspect the docker image.


## FAQ
**Q:** Can we setup a Drupal 8/9 - Vanilla using this?

**A:** Yes, we can do that. Given we have separate containers for PHP, Apache and MySql so we can setup Vanilla Drupal 8 using composer so easily. Inside the PHP container, just run the below command and create/setup the database accordingly.
```
composer create-project drupal/recommended-project my_site_name_dir
```
Drupal 9
```
composer create-project drupal/recommended-project:9.0.0 my_site_name_dir
```

More info - https://www.drupal.org/docs/develop/using-composer/using-composer-to-install-drupal-and-manage-dependencies

**Q:** How to exit from a container?

**A:** To exit from a container, just type `exit` in terminal.  

**Q:** How to access vi editor in MySql container?

**A:** Login to MySql container using `docker exec` command. For example, `c19d6217dc5c` is MySql container id in our case. 
```
docker exec -it c19d6217dc5c /bin/bash 
```
Run `apt-get update` commad and install vim using below command:
```
apt-get install vim
```
Now vi editor is available in MySql container.

## Recommendation
This stack has all the basic Docker images (PHP, Apache, and MySQL) and needs to be updated as per the project requirements. Though it runs successfully in the local environment, it's not recommended to use on production environment directly. The idea is to use it as a basic Docker stack, learn and make it available for local development with minimal efforts. In the long run either you can enhance it or may switch on docker4drupal which is a more advanced and powerful Docker image.

## References
* https://www.drupal.org/node/2736447
* https://hub.docker.com/r/wodby/drupal-php/dockerfile/
* https://github.com/mzazon/php-apache-mysql-containerized
* https://itnext.io/local-drupal-8-development-with-docker-ed25910cfce2
* https://duvien.com/blog/using-docker-setup-test-environment-drupal-8-less-2-minutes
* https://github.com/Lullabot/drupal-docker-boilerplate/blob/master/README.md
