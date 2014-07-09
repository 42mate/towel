<?php

/**
 * Default routes for User Controller, copy this file
 * to yourapp/config/routes to enabled this feature.
 */

$controller = get_app()->getInstance('user_controller');

add_route('get', 'login', array(
    'controller' => $controller,
    'action' => 'loginShow',
    'route_name' => 'login',
));

add_route('post', 'login', array(
    'controller' => $controller,
    'action' => 'loginAction',
    'route_name' => 'login_post',
));

add_route('get', 'logout', array(
    'controller' => $controller,
    'action' => 'logoutAction',
    'route_name' => 'logout',
));

add_route('get', 'user', array(
    'controller' => $controller,
    'action' => 'profileShow',
    'route_name' => 'user',
));

add_route('get', 'user/register', array(
    'controller' => $controller,
    'action' => 'registerShow'
));

add_route('post', 'user/register', array(
    'controller' => $controller,
    'action' => 'registerAction',
    'route_name' => 'user_register',
));

add_route('get', 'user/recover', array(
    'controller' => $controller,
    'action' => 'recoverShow',
    'route_name' => 'user_recover',
));

add_route('post', '/user/recover', array(
    'controller' => $controller,
    'action' => 'recoverAction',
    'route_name' => 'user_recover_post',
));