<?php

/**
 * Default routes for User Controller, copy this file
 * to yourapp/config/routes to enabled this feature.
 */

use Symfony\Component\HttpFoundation\Request;

$controller = new Towel\MVC\Controller\User();

$app->get('/login', function () use ($controller) {
    return $controller->loginShow();
});

$app->post('/login', function (Request $request) use ($controller) {
    return $controller->loginAction($request->get('data'));
});

$app->get('/logout', function () use ($controller) {
    return $controller->logoutAction();
});

$app->get('/user', function (Request $request) use ($controller) {
    return $controller->profileShow();
});

$app->get('/user/register', function (Request $request) use ($controller) {
    return $controller->registerShow();
});

$app->post('/user/register', function (Request $request) use ($controller) {
    return $controller->registerAction($request->get('data'));
});

$app->get('/user/recover', function (Request $request) use ($controller) {
    return $controller->recoverShow();
});

$app->post('/user/recover', function (Request $request) use ($controller) {
    $data = $request->get('data');
    return $controller->recoverAction($data);
});