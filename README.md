Containerize : PHP and Apache and Composer/MySQL/phpMyAdmin
===================================

### Introduction 
In this continuation of the Containerize This! series, we look at common web application technologies and how to use them effectively in Docker containers. PHP/Apache/MySQL have a very large market share in content management systems and web applications on the web, and with so many developers using these technologies, there is a lot of interest in modernizing the way they are used from local development to production. Today we'll take a look at different ways to containerize and link PHP, Apache and MySQL and show you some tips, tricks and best practices that will help you take a modern approach to developing and deploying your PHP applications!

For this demo, there are 6 simple files that you can download from git clone https://github.com/amirzibaee/docker-compose-php-mysql-apache
clone or simply copy and paste from this post to replicate the following folder structure. Please note that some Docker and security principles have been skipped here for simplicity and demonstration purposes. These include the use of PHP with root credentials, hard-coded/weak MySQL passwords, and the lack of SSL, to name a few! Do not run this code in production!



```shell
/docker-apache-php-mysql/
├── .env.example
├── apache-php
│   ├── php.ini
│   ├── Dockerfile
│   └── demo.apache.conf
├── docker-compose.yml
└── www
    └── index.php
```

You also need an .env file for the main variable. Please change .env.example to .env

```dotenv
# PHP/Apache Docker
PHP_VERSION=7.2
APACHE_VERSION=2.4.32
APACHE_EXTERN_PORT=80
PROJECT_ROOT=./www

# Mysql Docker
MYSQL_VERSION=5.7
MYSQL_EXTERN_PORT=3306
DB_ROOT_PASSWORD=root
DB_NAME=testdb
DB_USERNAME=app
DB_PASSWORD=app
```
Once this structure is replicated or cloned with these files and Docker is installed locally, you can simply run "docker-compose up" from the root of the project to launch this entire demo, and point your browser (or curl) to http://localhost:80 to see the demo. What "docker-compose" is and what this basic demo is all about will be explained in the following sections!
We will use the following simple PHP application to demonstrate everything:

#### index.php
```php
<h1>Hello World!</h1>
<h4>Attempting a MySQL connection from PHP ...</h4>
<?php
$DB_HOST = 'mysql';
$DB_USER = 'app';
$DB_PASS = 'app';
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
} else {
    echo "Connection to MySQL successfully established!";
}
?>

```
This code attempts to connect to a MySQL database via PHP's mysqli interface. If it succeeds, it prints a success message. If not, it prints an error message.

### Docker Compose

This format allows you to define groups of services that make up an entire application. It allows you to define the dependencies for these services, networks, volumes, etc. as code.

#### docker-compose.yml
```yaml
version: "3.2"
services:

# Apache with PHP
  apache3:
    build:
      context: './apache-php/'
      args:
        PHP_VERSION: ${PHP_VERSION}
    depends_on:
      - mysql
    networks:
      - frontend
      - backend
    ports:
      - ${APACHE_EXTERN_PORT}:80
    volumes:
      - ${PROJECT_ROOT}/:/var/www/html/
      - ./apache-php/conf/demo.apache.conf:/usr/local/apache2/conf/demo.apache.conf
      - ./apache-php/php.ini:/usr/local/etc/php/php.ini
    container_name: apache3

  mysql:
    image: mysql:${MYSQL_VERSION:-latest}
    restart: always
    ports:
      - ${MYSQL_EXTERN_PORT}:3306
    volumes:
            - ${DB_DATA_PATH}:/var/lib/mysql
    networks:
      - backend
    environment:
      MYSQL_ROOT_PASSWORD: "${DB_ROOT_PASSWORD}"
      MYSQL_DATABASE: "${DB_NAME}"
      MYSQL_USER: "${DB_USERNAME}"
      MYSQL_PASSWORD: "${DB_PASSWORD}"
    container_name: mysql

  phpmyadmin:
    depends_on:
      - mysql
    image: phpmyadmin/phpmyadmin
    ports:
      - ${PMA_EXTERN_PORT}:80
    environment:
      PMA_HOST: mysql
      MYSQL_USER: app
      MYSQL_PASSWORD: app
      UPLOAD_LIMIT: 5000M
    networks:
      - frontend
      - backend
    container_name: phpmyadmin


networks:
  frontend:
  backend:
volumes:
    data:

```
### Change Directory Permission
```
chown -R www-data:www-data ./www/
```

### Use
```
http://localhost
```
phpMyAdmin
```
http://localhost:8009
```
Connection to MySQL from PHP Docker
```
mysql:3306
```
Connection to MySQL external
```
localhost:3306
```

