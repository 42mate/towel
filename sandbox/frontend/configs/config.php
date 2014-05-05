<?php

//Configurations
global $appConfig;

define('APP_ROOT_DIR',    dirname(__FILE__) . '/../..');
define('APP_WEB_DIR',     APP_ROOT_DIR . '/web');
define('APP_UPLOADS_DIR', APP_WEB_DIR  . '/uploads');
define('APP_LIB_DIR',     APP_ROOT_DIR . '/vendor');
define('APP_FW_DIR',      APP_ROOT_DIR . '/vendor/42mate/Towel');
define('APP_CONFIG_DIR',  dirname(__FILE__));
define('APP_DIR',         dirname(__FILE__) . '/../Frontend');
define('APP_BASE_URL',    '/');

$appConfig = array(
	'doctrine' => array(
		'dbs.options' => array(
            'default' => array(
                'driver'    => 'pdo_mysql',
                'host'      => 'localhost',
                'dbname'    => 'db_name',
                'user'      => 'db_user',
                'password'  => 'db_pass',
                'charset'   => 'utf8',
            ),
		),
	),

	'twig' => array(
		'twig.path' => array(
            APP_DIR . '/Views',
            APP_FW_DIR . '/MVC/Views'
        )
	),

	'sessions' => array(
		'name' => '_SESS_APP',
		'cookie_lifetime' => 0,
        'session.storage.save_path' => '/tmp/',
	),

	'debug' => true,
);
