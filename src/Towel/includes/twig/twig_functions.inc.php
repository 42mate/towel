<?php

/**
 * Defines custom twig Functions
 */

$app = get_app();

/**
 * Renders an url with the given parameters.
 *
 * {{ url('/full/path/manual/Route') }}
 *
 * or for named routes.
 *
 * {{ url('route_name', { id: 69 }) }}
 *
 */
$url = new Twig_SimpleFunction('url', function ($route, $parameters = array(), $absolute = false) {

    return url($route, $parameters, $absolute);
});
$app->twig()->addFunction($url);


/**
 * Renders all messages. Use this for global messages.
 *
 * {{ render_messages() }}
 */
$render_messages = new Twig_SimpleFunction('render_messages', function() {
   $app = get_app();
   $out = '';
   $messages = $app->session()->get('messages');

   foreach ($messages as $message) {
       $out .= "<div class='alert alert-{$message->mt}'>{$message->content}</div>";
   }
   return $out;
});
$app->twig()->addFunction($render_messages);

/**
 * Assets url
 */
$assets_url = new Twig_SimpleFunction('assets_url', function ($application, $path) {
    return assets_url($application, $path);
});
$app->twig()->addFunction($assets_url);

