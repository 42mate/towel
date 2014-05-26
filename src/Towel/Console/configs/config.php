<?php
define('APP_ROOT_DIR', dirname(__FILE__) . '/../../../../../../..');
define('APP_WEB_DIR', APP_ROOT_DIR . '/web');
define('APP_UPLOADS_DIR', APP_WEB_DIR . '/uploads');
define('APP_LIB_DIR', APP_ROOT_DIR . '/vendor');
define('APP_FW_DIR', APP_ROOT_DIR . '/vendor/42mate/towel/src/Towel');
define('APP_CONFIG_DIR', dirname(__FILE__));
define('APP_DIR', dirname(__FILE__) . '/../..');
define('COMMANDS_DIR', dirname(__FILE__) . '/../Command');
define('COMMANDS_NAMESPACE', '\Towel\Console\Command' );

$appConfig = array(
    'twig' => array(
        'twig.path' => array(
            APP_FW_DIR . '/Views'
        )
    ),
    'class_map' => array(
        'user_controller' => '\Towel\MVC\Controller\User',
        'user_model' => '\Towel\MVC\Model\User',
        'app' =>  '\Towel\BaseApp',
    ),

    'debug' => true,
);