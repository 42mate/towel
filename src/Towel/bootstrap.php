<?php

/**
 * This boostrap is the base boostrap, include this file in your app boostrap
 * after of the configuration and do your custom boostrap stuff after the
 * inclusion.
 */
define('MESSAGE_SUCCESS', 'success');
define('MESSAGE_INFO', 'info');
define('MESSAGE_DANGER', 'danger');
define('MESSAGE_WARNING', 'warning');

//Init App
global $app;
$app = new Silex\Application();
$app['debug'] = $appConfig['debug'];
$app->register(new Silex\Provider\DoctrineServiceProvider(), $appConfig['doctrine']);
$app->register(new Silex\Provider\TwigServiceProvider(), $appConfig['twig']);
$app->register(new Silex\Provider\SessionServiceProvider(), $appConfig['sessions']);

//Init Routes
foreach (glob(APP_FW_DIR . "/includes/*.inc.php") as $includeFiles) {
    require_once "$includeFiles";
}

//Init Routes
foreach (glob(APP_CONFIG_DIR . "/routes/*Routes.php") as $routeFile) {
	require_once "$routeFile";
}

//Process Session messages.
$app['twig']->addGlobal('messages', $app['session']->get('messages'));
$app['twig']->addGlobal('fw_app', new \Towel\BaseApp());
$app['session']->set('messages', array());
