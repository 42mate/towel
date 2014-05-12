<?php

/**
 * Define custom twig Functions
 */

$app = get_app();

$url = new Twig_SimpleFunction('url', function ($route, $parameters = array(), $absolute = false) {
    return url($route, $parameters, $absolute);
});

$app->twig()->addFunction($url);