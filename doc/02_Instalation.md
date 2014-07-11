Installation
============

This is the long installation guide, if you are an expert PHP developer this will be a pice of cake for you,
if you are a newbie pay attention.

### Composer

Start by installing composer, be a man, install composer globally and have it in your PATH.
After that, run composer to create Towel project's folder structure.

### Installing Towel

```
composer create-project --no-interaction -s dev 42mate/towel-standard
```

This is going to create a directory called towel-standard (you can rename it if you want).

### Setting Up a MySQL Database

Assuming that you have a mysql server up and running, you have to access to the mysql console, create a database and create
an user.

```
$mysql -u root -p
> CREATE DATABASE towel;
> GRANT ALL PRIVILEGES ON towel.* TO 'towel_usr'@'localhost' IDENTIFIED BY 'towel_pass';
```

Replace towel for a valid database name, towel_usr for your towel user name for the database connection and towel_pass for
a valid mysql user password. These credentials are the default credentials for the Frontend sample application.

### Setting Up a Nginx Web Server

Assuming that you have a nginx and php-fpm up and running this will be the virual host configuration.

```
server {
    listen 80;
    server_name www.yourservername.com; #SET A USEFUL SERVER NAME

    root /path/to/towel/towel-standard/web; #SET THE FULL PATH TO YOUR INSTALLATION AND USE THE web FOLDER AS DOCROOT

    index index.php;

    location / {
        try_files $uri @rewriteapp;
    }

    location @rewriteapp {
        rewrite ^(.*)$ /index.php/$1 last;
    }

    location ~ ^/(index)\.php(/|$) {
        include fastcgi.conf;
        # SELECT YOUR CONNECTION MODE TO FPM
        fastcgi_pass 127.0.0.1:9000;
        #fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        fastcgi_param HTTPS off;
        fastcgi_intercept_errors on;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_read_timeout 10m;
    }


    # YOU CAN CHANGE THE FILE NAMES FOR LOGS
    error_log /var/log/nginx/towel_error.log;
    access_log /var/log/nginx/towel_access.log;
}
``

### Setting Up an Apache Web Server (optional).

Help here !.

### PHP Extras

We recommend you to install the following PHP extension

memcached for caching
xdebug for development
