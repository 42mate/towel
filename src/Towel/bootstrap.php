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
global $silex;
$silex = new Silex\Application();
$silex['debug'] = $appConfig['debug'];
$silex->register(new Silex\Provider\DoctrineServiceProvider(), $appConfig['doctrine']);
$silex->register(new Silex\Provider\TwigServiceProvider(), $appConfig['twig']);
$silex->register(new Silex\Provider\SessionServiceProvider(), $appConfig['sessions']);
$silex->register(new Silex\Provider\UrlGeneratorServiceProvider());

//Init Routes
foreach (glob(APP_FW_DIR . "/includes/*.inc.php") as $includeFiles) {
    require_once "$includeFiles";
}

//Init Routes
foreach (glob(APP_CONFIG_DIR . "/routes/*Routes.php") as $routeFile) {
    require_once "$routeFile";
}

//Init Twig Functions
require_once APP_FW_DIR . "/twig/twig_functions.inc.php";

//Init Twig Filters
require_once APP_FW_DIR . "/twig/twig_filters.inc.php";

//Process Session messages.
$silex['twig']->addGlobal('fw_app', new \Towel\BaseApp());
$silex['session']->set('messages', array());

