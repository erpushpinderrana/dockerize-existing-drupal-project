# Dockerize an Existing Drupal Project
Drupal 8 is not new now and most developers have started using Docker for local development. However, few developers still struggle to setup Docker locally. 
There are tons of documents available on the Internet which describe how to set up a local Docker + Drupal development environment. 
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
