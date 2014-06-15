<?php

$appConfig = array_merge($appConfig, array(
    'doctrine' => array(
        'dbs.options' => array(
            'default' => array(
                'driver' => 'pdo_sqlite',
                'path' => dirname(__FILE__) . '/../Tests/Resources/modelTest',
                'charset' => 'utf8',
            ),
        ),
    ))
);
