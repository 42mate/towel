<?php

/**
 * Default routes for User Controller, copy this file
 * to yourapp/config/routes to enabled this feature.
 */

$controller = get_app()->getInstance('user_controller');

add_route('get', '/login', array(
    'controller' => $controller,
    'action' => 'loginShow'
));

add_route('post', '/login', array(
    'controller' => $controller,
    'action' => 'loginAction'
));

add_route('get', '/logout', array(
    'controller' => $controller,
    'action' => 'logoutAction'
));

add_route('get', '/user', array(
    'controller' => $controller,
    'action' => 'profileShow'
));

add_route('get', '/user/register', array(
    'controller' => $controller,
    'action' => 'registerShow'
));

add_route('post', '/user/register', array(
    'controller' => $controller,
    'action' => 'registerAction'
));

add_route('get', '/user/recover', array(
    'controller' => $controller,
    'action' => 'recoverShow'
));

add_route('post', '/user/recover', array(
    'controller' => $controller,
    'action' => 'recoverAction'
));