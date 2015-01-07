# Views

## Introduction

We use twig to create our templates, twig is a great template engine and allow us to create templates very easy. So
as a first step you must learn about twig.

http://twig.sensiolabs.org/

Also we are using Twitter Boostrap and jQuery by default, if you want to remove it you can do it, but our documentation will
assume that you want to use it.

http://getbootstrap.com/
http://jquery.com/

## How our views works

Once you have the route and your controller for your action you'll need a view to present something to the user. In order
to render a view you have to define a template.

In your Application, Inside of the Views folder you must create your twig templates and define you application assets.

We recommend you to create a master.twig file into your views folder of your main application, that master template must have the master layout of the page.

Your specific pages for your specific actions must inherit of the master.twig template.

Define your assets into assets folder and use assets_url function to get the right path to your assets.

### Defining Twig Paths

In order to find your templates twig loaders needs to know about your view Folders, Towel provides a few
methods to add application views folder to your current Twig loader.

What the loader does is build a collection of twig paths, twig is going to use that collection to lookup for views
when you need to render it, the order of the path in the collections is very important since the first path is used to
lookup for the requested template, if is not found there use the next one and goes on until find the template or fails.

The default application must be the first place to lockup, secondary apps must follow the default.

Add your default twig app with

```php
add_app_twig('Frontend', true);
```

And any other app path with

```php
add_app_twig('SecondaryApp');
```

The execution order of this instruction will determine the order of the lookup path.

If you want to override any template from others application you can do it easily defining a template with the same name and a same
relative path, from Views, in your default application since that is the one with more priority will be loaded first than the one in the
secondary application.

### Master Template

This is a master.twig sample from the Frontend application

```html
<!DOCTYPE html>
<html lang="en">
    <head>
        {% block head %}
            <title>Towel Framework</title>
        {% endblock %}
        {% block header_meta %}
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        {% endblock %}
        {% block header_css %}
            <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        {% endblock %}
        {% block header_js %}
            <script src="/vendor/jquery/jquery.min.js"></script>
            <script src="/vendor/bootstrap/js/bootstrap.min.js"></script>
            <script src="{{ assets_url('Frontend', 'js/frontend.js') }}"></script>
        {% endblock %}
        <link href="{{ assets_url('Towel', 'css/towel.css') }}" media="screen, projection" rel="stylesheet" type="text/css" />
        <link href="{{ assets_url('Frontend', 'css/towel.css') }}" media="screen, projection" rel="stylesheet" type="text/css" />
        <link href="{{ assets_url('Frontend', 'css/screen.css') }}" media="screen, projection" rel="stylesheet" type="text/css" />
        <link href="{{ assets_url('Frontend', 'css/print.css') }}" media="print" rel="stylesheet" type="text/css" />
        <!--[if IE]>
        <link href="/css/ie.css" media="screen, projection" rel="stylesheet" type="text/css" />
        <![endif]-->
    </head>
    <body>
        <div class="page container-fluid">
            <div class="header row">
                <div class="col-md-12">
                    {% block header %}
                        <header>
                            <h1>Welcome To Towel</h1>
                        </header>
                    {% endblock %}
                </div>
            </div>
            <div class="content row">
                {% include 'templates/_menu.twig' %}
                <div id=" col-md-12 messages">{{ render_messages() | raw }}</div>
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div id="content">
                        {% block content %}
                        {% endblock %}
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
            <div class="footer row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <footer id="footer">
                        {% block footer %}
                            42mate - 2014
                        {% endblock %}
                    </footer>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
        {% block footer_css %}
        {% endblock %}
        {% block footer_js %}
        {% endblock %}
    </body>
</html>
```

### Action Templates

Your action templates must inherit from the master.twig and then define the needed blocks for your action.
If you controller is called MyController, create a new folder called MyController and put inside of that your twig templates
for your controller actions, this is not a hard rule but is going to be a good practice.

```
MyApplication
 \Controller
    MyControllerA
    MyControllerB
 \Views
    \assets
    master.twig
    \MyControllerA
      ActionA.twig
      ActionB.twig
    \MyControllerB
      ActionA.twig
      ActionB.twig
```

This is a template for an action sample

```html
{% extends "master.twig" %}

{% block content %}
    <h2>Blog</h2>
    {% if is_authenticated() == true %}
        <div class="actions">
            <a href="{{ url('blog_create') }}">New</a>
        </div>
    {% endif %}
    <div class="posts">
        {% for post in posts %}
            <div class="post">
                <div class="title">
                    <a href="{{ url('blog_view', { 'id' : post.record.id }) }}">
                        {{ post.record.title }}
                    </a>
                </div>
                <div class="info">
                    <div class="date">
                        Publicado en : {{ post.record.created_at | date("d/m/Y") }}
                    </div>
                </div>
            </div>
        {% else %}
            <div class="alert-info">
                There is no posts
            </div   >
        {% endfor %}
    </div>
{% endblock %}
```

## Render the template in the controller

Once you have defined the template you can call to render it from the action in the controller

```php
return $this->twig()->render('MyController\MyTemplate.twig', array('var1' => $var1));
```

This says to twig, that we want to render the template MyTemplate.twig in MyController sending to the template the variable $var1 and in the template will named var1.

Note the return, remember that all controllers must return an string or response object, since we are going to render the response
we are ready to return it as response of the controller.

Twig will look into the twig paths for MyController\MyTemplate.twig staring in the default application and then continuing with the remaining paths.

If twig can not find a suitable template will throw an error.

## Assets

Since the application assets (css, js, images) are not public, they are inside of the application (Application/Views/assets)
Towel provides a controller to server application assets.

To use it you only need to add any kind of assets in to your application asset folder.

```
Application/Views/assets
```

To reference these assets you have to use the function assets_url, available in PHP and in Twig.

PHP

```
assets_url('ApplicationName', 'css/myCool.css');
```

Twig

```
<script type="text/javascript" src="{{ assets_url('Frontend', 'js/frontend.js') }}"></script>
```

This function is going to return a valid url to be caught by the assets controller that is the one in charge to
return the assets content.

Security Concerns : Since this controllers accepts a route that is going to be used to read the content and then
printed out to the client this can be a security concern, that is why we are only accepting requests for media
content css and js, any other kind of assets will not be serve.

Also we are not serving any content outside of the assets folder, so don't put anything interesting in that folder.


### Referencing Images in CSS

To reference images in the css we don't have any cool method yet, but you can build the url manually, for example.

```
background-url: url('/assets?application=Frontend&path=images/background.png');
```

If you use sass this will be much more easy, you can do a mixin to get the url.

```css
@function assets_url($path) {
  @return '/assets?application=' + $application + '&path=' + $path ';';
}
```

## Twig Functions Helpers

### if_not_empty

Checks if a model field is empty (0, false, null or not defined), if is not empty will return the value of the field
if is empty will return an empty string.

Sample


```
<input type="text" name="post[title]" value="{{ if_not_empty(post, 'title') }}" />
```

### is_authenticated

Checks if the user is authenticated.

### user_name

Returns the user name of the current logged user.

### render_messages

Renders the flash messages, use it in your master template.


# JavaScript Settings

Usually we need to send from our PHP backend things to our JavaScript frontend, like states, variables, arrays, objects
and others values or data structures.

Drupal have a Global object in JavaScript called Drupal where one of the fields in this object is settings, settings
have a lot of values that are injected in the PHP code using drupal_add_js. This feature of Drupal is very usefull
so we tried to do the same for towel.

In JavaScript you will have a Global object called Towel, this object will have a field settings and settings will have
 anything that we put inside using the function add_js_settings.

If we use add_js_settings like this

```php
add_js_settings(array('foo' => 'bar'));
```

In JavaScript you'll have

```javascript
console.log(Towel.settings.foo); //Value is bar
```

The parameter of add_js_settings must be an array, where the key, or keys, will become the field of the javascript object
and the value (can be anything) will be the value of the field in the javascript object.


## Before Use It

You'll have to print in your master twig template

```html
<script type="text/javascript">{{ js_settings() | raw }}</script>
``

In order to render the javascript code with the object values.

### For Global values, in All requests

You'll need to add this in the bootstrap file.

```php
get_app()->silex()->before(function (Request $request) {
    //Add code here for global injection, In All requests.
    add_js_settings(array(
        'field1' => 'value',
        'field2' => 'value'
    ));
});
```

NOTE : Keep in mind that the values will be set before of the controller execution, we haven't figured out
yet how to add settings global values after the controller execution since the HTML output was already rendered
and the settings were printed. Ideas are welcome :)

