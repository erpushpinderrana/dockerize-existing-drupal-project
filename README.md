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
6. Now put your codebase inside `docroot` folder that is pointed to `/var/www/html` in the PHP container. You can also see it using docker command as shown below:
```
docker exec -it 8294e1800058 /bin/sh
```
`8294e1800058` is PHP container id as shown in the above table.

7. Since the Drush and composer are already part of PHP docker image, you can simply import the database using drush.

8. This way the basic setup is done and you can change the composer file accordingly. In case any additional PHP library is needed, update the PHP docker file and rebuild the PHP Docker image.

## Detailed Explanation
--WIP---
