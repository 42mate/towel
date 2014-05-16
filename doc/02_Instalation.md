Installation
============

To install towel you'll need to know the basis of Composer.

### Composer ###

Start by installing composer in any preferred folder if you don't have it already installed.

````
    curl -s https://getcomposer.org/installer | php
````

After that, run composer to create Towel project's folder structure.

```
    php composer.phar create-project --no-interaction 42mate/towel-standard /path/to/towel-project-folder dev-master
```

This is going to create a directory called

After that you might want to

* Configure your virtual host to the web folder.
* Setup your application config.

After that you'll have a fully operational Towel instance to start coding your application.
