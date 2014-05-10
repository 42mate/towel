<?php

/**
 * Default routes for User Controller, copy this file
 * to yourapp/config/routes to enabled this feature.
 */

$app = new \Towel\MVC\Controller\BaseController();
$controller = $app->getInstance('user_controller');
add_route('get', '/login', array($controller, 'loginShow'));
add_route('post', '/login', array($controller, 'loginAction'));
add_route('get', '/logout', array($controller, 'logoutAction'));
add_route('get', '/user', array($controller, 'profileShow'));
add_route('get', '/user/register', array($controller, 'registerShow'));
add_route('post', '/user/register', array($controller, 'registerAction'));
add_route('get', '/user/recover', array($controller, 'recoverShow'));
add_route('post', '/user/recover', array($controller, 'recoverAction'));
