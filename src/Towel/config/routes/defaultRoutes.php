<?php

/**
 * Default Routes for / , 404 and 500 pages.
 */

use Symfony\Component\HttpFoundation\Request;

$controller = new Towel\MVC\Controller\BaseController;

// Routes
$app->get('/', function (Request $request) use ($controller) {
    return $controller->index();
});

if ($appConfig['debug']) {
    $app->error(function (\Exception $e) use ($controller) {
        return $controller->routeError($e);
    });
}