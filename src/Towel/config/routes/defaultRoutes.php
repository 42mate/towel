<?php

$controller = new Towel\MVC\Controller\BaseController();

add_route('get', '/', array($controller, 'index'));

if ($appConfig['debug']) {
    get_app()->silex()->error(function (\Exception $e) use ($controller) {
        return $controller->routeError($e);
    });
}