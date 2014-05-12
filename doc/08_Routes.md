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

add_route('get', "/myRoute", array(
        'controller' => new Frontend\Controller\MyController(),
        'action' => 'myPublicMethod',
        'route_name' => 'controller/method/{id}',
        'secure' => false,
    )
);
````

### Important ###

* My Controller can be any object, not necessarily an object derived of BaseController.
* myPublicMethod will receive a Request Object (\Symfony\Component\HttpFoundation\Request),
* Towel is going to execute the method of the controller.
* myPublicMethod must return the Content for the response, if nothing is returned an exception will be thrown.
* Id is going to be available with the request object (```$request->get('id')````);
* add_route is just a shortcut for what Silex does, you can add any custom code here and it will work.