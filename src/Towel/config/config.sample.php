<?php

$appName = basename(dirname(dirname(__FILE__)));
global $appConfig;

define('APP_NAME', $appName);

if (APP_NAME == 'Towel') {
    define('APP_ROOT_DIR', dirname(__FILE__) . '/../../../../../..');
} else {
    define('APP_ROOT_DIR', dirname(__FILE__) . '/../../..');
}

define('APP_CACHE_DIR', APP_ROOT_DIR . '/cache/' . APP_NAME);
define('APP_WEB_DIR', APP_ROOT_DIR . '/web');
define('APP_UPLOADS_DIR', APP_WEB_DIR . '/uploads');
define('APP_LIB_DIR', APP_ROOT_DIR . '/vendor');
define('APP_FW_DIR', APP_ROOT_DIR . '/vendor/42mate/towel/src/Towel');
define('APP_CONFIG_DIR', dirname(__FILE__));
define('APP_DIR', dirname(__FILE__) . '/../');
define('APP_BASE_URL', '/');

$appConfig = array(
    'doctrine' => array(
        'dbs.options' => array(
            'default' => array(
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'dbname' => 'db_name',
                'user' => 'db_user',
                'password' => 'db_pass',
                'charset' => 'utf8',
            ),
            /**
             * 'mysql_two' => array(
             *   'driver'    => 'pdo_mysql',
             *   'host'      => 'mysql_write.someplace.tld',
             *   'dbname'    => 'my_database',
             *   'user'      => 'my_username',
             *   'password'  => 'my_password',
             *   'charset'   => 'utf8',
             *   ),
             */
        ),
    ),

    'twig' => array(
        'twig.path' => array(
            APP_DIR . '/Views',
            APP_FW_DIR . '/Views'
        )
    ),

    'sessions' => array(
        'name' => '_SESS_APP',
        'cookie_lifetime' => 0,
        'session.storage.save_path' => '/tmp/',
    ),

    'class_map' => array(
        'app' => '\Towel\BaseApp',
        'user_model' => '\Towel\Model\User',
        'user_controller' => '\Towel\Controller\User',
    ),

    'cache' => array(
        'driver' => 'memcached',
        'prefix' => 'towel',
        'options' => array(
            'hosts' => array(
                '127.0.0.1' => 11211
            ),
        ),
    ),

    'debug' => true,
);

/**
 * Use HTTP_HOST.config.php file to override any default configuration
 * for in a specific environment.
 */
if (!empty($_SERVER['HTTP_HOST'])) {
    $local_config = dirname(__FILE__) . '/' . $_SERVER['HTTP_HOST'] . 'config.php';
    if (file_exists($local_config)) {
        include $local_config;
    }
}