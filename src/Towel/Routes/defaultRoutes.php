<?php

add_route('get', '/', array(
        'controller' => new \Towel\Controller\BaseController(),
        'action' => 'index',
        'route_name' => 'home'
    )
);

add_route('get', '/assets', array(
    'controller' => new \Towel\Controller\AssetsController(),
    'action' => 'index',
    'route_name' => 'assets'
));