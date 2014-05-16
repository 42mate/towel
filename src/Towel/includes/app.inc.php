<?php

/**
 * Returns the App.
 *
 * @return \Towel\BaseApp
 */
function get_app()
{
    global $appConfig;
    $appClass = $appConfig['class_map']['app'];
    return new $appClass();
}

/**
 * Returns a Base controller.
 *
 * @return \Towel\MVC\Controller\BaseController
 */
function get_base_controller()
{
    return new \Towel\MVC\Controller\BaseController();
}