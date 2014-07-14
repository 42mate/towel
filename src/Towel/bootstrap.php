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

$appConfig['twig']['twig.options']['cache'] = (!empty($appConfig['twig']['twig.options']['cache']))
                                              ? $appConfig['twig']['twig.options']['cache']
                                              : APP_CACHE_DIR . '/twig';

$appConfig['twig']['twig.options']['autor_reload'] = (!empty($appConfig['twig']['twig.options']['autor_reload']))
                                                     ? $appConfig['twig']['twig.options']['autor_reload']
                                                     : $appConfig['debug'];

$silex->register(new Silex\Provider\DoctrineServiceProvider(), $appConfig['doctrine']);
$silex->register(new Silex\Provider\TwigServiceProvider(), $appConfig['twig']);
$silex->register(new Silex\Provider\SessionServiceProvider(), $appConfig['sessions']);
$silex->register(new Silex\Provider\UrlGeneratorServiceProvider());

//Init Functions
foreach (glob(APP_FW_DIR . "/includes/*.inc.php") as $includeFiles) {
    require_once "$includeFiles";
}

//Init Default Routes
foreach (glob(APP_FW_DIR . "/Routes/*Routes.php") as $defaultRoutes) {
    require_once "$defaultRoutes";
}

//Init Twig Functions
require_once APP_FW_DIR . "/includes/twig/twig_functions.inc.php";

//Init Twig Filters
require_once APP_FW_DIR . "/includes/twig/twig_filters.inc.php";

//Process Session messages.
$silex['twig']->addGlobal('fw_app', new \Towel\BaseApp());