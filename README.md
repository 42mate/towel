Towel ![Build Info](https://travis-ci.org/42mate/towel.svg?branch=master)
=====

Towel is a PHP MVC framework designed to be very very easy to learn. It is built on top of Silex and Doctrine Dbal
and adds an extra layer to provide ease of integration and the capability to work like any MVC Framework.

## Motivation


* Almost all PHP Frameworks are very hard to learn for a newbie.
* Several frameworks follows a lot of theorethical concepts but become very impractical.
* We know that not all PHP Developers in the community are Computer Scientists.
* We've worked with PHP for years, we love its practicing and we want to keep it that way.
* We hate updates that break everything every six months, we want long term support.
* NIH: Everybody has their own framework, we want our own.
* We are NOT fans of Java. 


## What we did

* Silex is great but is hard to maintain if the project start to grow.
* Dbal is great and solves 99.9 % of the database access problems.
* We just added a couple of classes to mix these great tools and make it more easy and standard for use.

## What do we want ?

* A framework easy to teach to new PHP Developer
* A framework that scale
* A framework driven by the Database and not by an Schema
* Keep it Simple, Stupid: not a lot of options, just the right options; not a lot of features, just the right features

## Why Towel

A towel, The Hitchhiker's Guide to the Galaxy says, is about the most massively useful thing an interstellar hitchhiker can have.
Always know where your towel is.

More info [here](http://hitchhikers.wikia.com/wiki/Towel)

## What do I have to know ?

* PHP, basics and Object Oriented programming, Namespaces.
* Composer, how it works, PSR-0 autoloader
* Silex
* Dbal and of course SQL
* Twig

## Requirements

* Unix Environment
* PHP 5.4 or higher
* Composer
* A Web Server
* MySQL (optional)

## Installation

Start by installing composer in any preferred folder if you don't have it already installed.

````
    curl -s https://getcomposer.org/installer | php
````

After that, run composer to create Towel project's folder structure.

```
    php composer.php create-project --no-interaction -s dev 42mate/towel-standard
```

After that you'll have a fully operational Towel instance to start coding your application.

After that you might want to

* Configure your virtual host to the web folder.
* Setup your application config.

## Contributions to Towel

If want to contribute to Towel's core you'll need to do a few extra steps (no worries, they're not much).


1 - Install Towel Standard Distribution for testing your your in towel

```
php composer.php create-project --no-interaction -s dev 42mate/towel-standard
```

2 - Once is installed remove the folder vendor/42mate/towel

3 - Fork Towel repository with your git account into your own fork.

4 - Go to vendor/42mate and clone your towel fork
 
  ```  git clone git@github.com:USERNAME/towel.git ```
  
5 - Add the upstream repository as a remote (for update your fork).

   ```
     cd towel
     git remote add upstream git://github.com/42mate/towel.git 42mate/towel
     git config core.ignorecase false
   ```
  
6 - After cloning Towel core, you only need to setup your favourite http server to serve pages from Towel's application.

7 - Start your contributions!

NOTE : If you need to add or update or do anything with composer, composer will complain about changes in the 42mate/towel working copy. So in that case you'll have to delete your fork, install with composer the latest 42mate/towel relased (composer update after delete vendor/42mate/towel), then do your compoer magic and when is done repeat this to get again your working copy from your fork. If anyone have a better workflow we will really apreciate the suggestion.

## Update your fork ##

To update your fork with the main repo do the following

```
  cd vendor/42mate/towel
  git checkout master
  git fetch upstream
  git merge upstream/master
```

Then you'll have the latest main repo changes in your fork, if everything works you can push to your fork and then create a pull request.

```
  git commit .... your params ...
  git push origin master
```

## Next Steps

Read or documentation to learn how to create models, controllers, routes and views to create
the most awesome apps that you ever imagine !

