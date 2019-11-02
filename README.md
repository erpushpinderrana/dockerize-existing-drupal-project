# Dockerize an Existing Drupal Project
Drupal 8 is not new now and most developers have started using Docker for local development. However, few developers still struggle to setup Docker locally. 
There are tons of documents available on the Internet which describe how to set up a local Docker + Drupal development environment. They are really helpful but most of them come with their own custom images.
Considering folks who are new to Docker find this documentation complicated sometimes because to run Docker locally requires a lot of configurations.
On top of that, there are multiple docker images available on Web, and developers who want to use docker in existing projects can't finalize
what is the right Docker image for their project. 

# Problem Statement
If an existing project development is already happening without Docker (maybe using XAMPP, WAMP, Acquia Dev Desktop or Vagrant etc.)
then developers don't want to spend much time on setting up it on Docker. They want to minimize the setup time and to do that they look for any relevant Docker image. Sometimes they get it easily but most of the time they find that there are some extra modules/configurations/components/libraries that are not needed in their project. Hence, they may look for plain PHP, Apache
and MySql image that they can update as per their requirements.

# Solution
1. Create separate containers for PHP, MySQL, and Apache.
2. Manage version for all the containers separately.
3. Whatever the libraries are needed, just add the same in the individual docker file.
4. Setup the local repository, error logs etc. path in the docker file itself.

# Let's start and follow the below steps to maintain an existing project using Docker.

## Prerequisites

1. [Docker](https://docs.docker.com/engine/installation/)
2. [Docker-Compose](https://docs.docker.com/compose/install/) - Needed for Linux
3. [Understanding of Composer](https://getcomposer.org/doc/00-intro.md/)


## Quick Setup
1. Clone this repository and run the below command:
```
docker-compose up -d
```
2. If everthing goes well, you will see below message:
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
5. With the current setup you will get below versions but you can change it anytime in .env file.
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
If it shows you `index.php` that means it mounts to your local repository i.e. `docroot`
6. Now hit the http://localhost:80 in browser to ensure setup is successful. Along with the below output, you will also see phpinfo() output.
```
Congratulations!! Docker setup connection is successful.
Checking MySQL integration with php.
MySql connection is successful!
```
7. Now the docker setup is done, you can put your local codebase inside the `docroot`. Now it's up to you whether you want to run multiple drupal instances within this repository or a single one. If you want to manage multiple repositories then keep the project folder inside the `docroot` repository. For example, in our case, it's `drupal8` repository.

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

8. Now update your project's database credentials in `settings.php` or `local.settings.php` wherever you are managing. As we are already in PHP container `/var/www/html/drupal8/web`, hence we can import the database using Drush:

```
drush sql:dump --result-file=../backup.sql
```
Here `backup.sql` is your mysql database backup file. It can different in your case.
9. Now access your drupal project in browser. For example, in our case it would be below URL: 
```
http://localhost/drupal8/web
```
We may create virtual host entry for above URL.
10. 

--WIP---

## Issues and Resolutions

**Issue:** While importing or creating the database, you may face this error `ERROR 1044 (42000): Access denied for user 'drupal'@'%'`

**Resolution:** You should connect with admin credentials. In our case, the user name is root and the password is admin.

**Issue:** `ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/ru` 

**Resolution:** Make sure correct host name is there. In our case, it's mysql.

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

--WIP---

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
## Important Docker commands
 * ``docker-compose up`` Start all the containers.
 * ``docker-compose down`` Stop all the containers.
 * ``docker system prune -a`` Delete all the docker images.
 * ``docker ps`` See all active containers.

## References
* https://hub.docker.com/r/wodby/drupal-php/dockerfile/
* https://github.com/mzazon/php-apache-mysql-containerized
* https://itnext.io/local-drupal-8-development-with-docker-ed25910cfce2
* https://duvien.com/blog/using-docker-setup-test-environment-drupal-8-less-2-minutes
* https://github.com/Lullabot/drupal-docker-boilerplate/blob/master/README.md

