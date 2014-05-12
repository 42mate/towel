Routes
======

Towel provides a cool API to define application routes, in the hood you'll see that uses Symfony Routing
system but our API is really easy to use.

## Defining Routes ##

The boostrap of Towel is going to load any file that ends with Routes.php (*Routes.php) inside of the routes folder
in configs of your app.

This Routes files are just PHP files that are going to be executed, so this is just PHP nothing fancy.

To define routes Towels provides the function add_route, this function receives the following parameters

 * **method** : get, post, put, delete
 * **route** : Any route expression valid for the Routing Matcher
 * **options** : An array with options.

Options must have defined two entries

 * **controller** : An instance of a valid controller.
 * **action** : The public method of the controller that is going to be executed. Action will receive a Request Object.

Optionally values are of options are

 * **route_name** : If you want to use this route pattern with named routes.
 * **secure** : Boolean, true if the route is only for authenticated users.

So in a nutshell here is an example of a route

````php
$controller = new Frontend\Controller\Post;
$entity = 'post';

add_route('get', "/controller/method/{id}", array(
        'controller' => new Frontend\Controller\MyController(),
        'action' => 'myPublicMethod',
        'route_name' => 'myRoute',
        'secure' => false,
    )
);
````

### Important ###

* My Controller can be any object, not necessarily an object derived of BaseController.
* myPublicMethod will receive a Request Object (\Symfony\Component\HttpFoundation\Request),
* Towel is going to execute the method of the controller.
* myPublicMethod must return the Content for the response, if nothing is returned an exception will be thrown.
* Id is going to be available with the request object (```$request->get('id')```);
* add_route is just a shortcut for what Silex does, you can add any custom code here and it will work.

## Build links ##

You can just create the urls by hand if you want but keep in mind that your app can be moved to another path and
your url pattern may change in the future.

To solve the first problem Towel have a constant called APP_BASE_URL, this is the path prefix for the site, is defined
in the config file and by default is /, if you have your app inside of a directory you may want to change it. If you build
your links manually remember to add this constant at the beginning.

````$url = APP_BASE_URL . 'controller/method/66';  // $url = /controller/method/66 ````

To build links Towel provides the url function, url will add the APP_BASE_URL for you.

```` url('/controller/method/66'); ````

NOTE : Always add the / for manual routes.

## Named Routes ##

If you use ***named routes*** you can use the name of the route in url function.

``` url('myRoute', array('id' => 66)); ```

Or in twig

``` {{ url('myRoute', { 'id' => 66 }) }} ```

Named Routes will solve the problem if the url of the patter changes, you'll only need to change the pattern in routes
and that's it, any place that use url will be updated automatically if the parameters of the url remains equals.

For named route you must not have to include / at the beginning of the name.

### Absolutes Urls ###

If you need absolutes urls (with the domain no just the path) add true as third parameter of url.


``` url('myRoute', array('id' => 66), true); ```

Or in twig

``` {{ url('myRoute', { 'id' => 66 }, true) }} ```

### Important ###

We recommend the use of named route always, it will save you a lot of work :)

## Resume ##

* Use ***add_route*** in APP_DIR/config/routes/*Routes.php files to define new routes.
* Use ***url*** to build route links in twig or php code.
* Try to use ***named routes*** always.
* Check ***APP_BASE_URL*** constant to verify if works for your environment (config file).