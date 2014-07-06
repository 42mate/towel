# Views

## Introduction

## Twig

## Create a Template

## Render the template in the controller

## Extending from the master template

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

## Adding Assets into your templates

## Twig configuration