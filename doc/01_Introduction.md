Welcome to Towel
===============

Towel is a web framework that born inside of 42mate as an internal framework to develop applications. We are total aware about the bad side of have your own framework but we been working a long time with Symfony, Cake, Zend Fraeworkd, Drupal and Wordpress and we are kind of tired of the speed of changed in most of those framework, we can even complete to learn the whole framework, complete a work and bum !, the framwork have a new relase and everything have changed.

Whit Towel we want to create an easy to learn framework, with long term support, having the best things of others framworks and the most important, following the PHP philosophy because this framework is for PHP developers, not for Java, .NET or whatever other crap language.

This framwork is built over two great products, Composer, Silex and Doctrine Dbal, we strongly recomend you start to learn those frameworks and tools first, they are very easy to learn. Also we are using Twig, Bootstrap, Sass, jQuery for the frontend.

## Features

Easy to learn
No dark magic inside, all is pretty straight forward
Like a sort to MVC
We talk SQL
Lightway
Uses Silex and Dbal

### Pros

You'll know what is happening in your app.
Is going to be fast.
We are not going to change all the framework once is stable.
We are covering only a specific PHP Ecosystem, we don't want to kill all the zombies with the same gun.

### Cons

You won't have a lot of great feature that others frameworks have.

## Instalation

Start by installing composer in any preferred folder if you don't have it already installed.

    curl -s https://getcomposer.org/installer | php
    
After that, run composer to create Towel project's folder structure.

    php composer.php create-project --no-interaction -s dev 42mate/towel-standard
    
After that you'll have a fully operational Towel instance to start coding your application.

Following you might want to

 * Configure your virtual host to the web folder.
 * Setup a Database.
 * Setup your application config.



