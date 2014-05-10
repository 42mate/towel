<?php

/**
 * Default Routes for / , 404 and 500 pages.
 */

$controller = new Towel\MVC\Controller\BaseController;

// Routes

add_route('get', '/', array($controller, 'index'));

if ($appConfig['debug']) {
    $app->error(function (\Exception $e) use ($controller) {
        return $controller->routeError($e);
    });
}