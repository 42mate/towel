<?php

/**
 * Returns the App.
 *
 * @return \Towel\BaseApp
 */
function get_app() {
    return get_instance('app', array(), true);
}

/**
 * Returns a Base controller.
 *
 * @return \Towel\Controller\BaseController
 */
function get_base_controller() {
    return new \Towel\Controller\BaseController();
}

/**
 * Gets an instance of the given class name.
 *
 * @param $classMapName : Class name.
 * @param array $args : Args for the constructor
 * @param $singleton : To get Singleton Classes.
 *
 * @return object
 */
function get_instance($classMapName, $args = array(), $singleton = false) {
    static $objects = array();
    global $appConfig;
    if ($singleton == false || empty($objects[md5($classMapName)])) {

        if (!empty($appConfig['class_map'][$classMapName])) {
            $className = $appConfig['class_map'][$classMapName];
        } else {
            $className = $classMapName; // No map, use the given name.
        }

        $class = new \ReflectionClass($className);
        $object = $class->newInstance($args);

        if ($singleton) {
            $objects[md5($classMapName)] = $object;
        }
    } else {
        $object = $objects[md5($classMapName)];
    }

    return $object;
}

/**
 * Var Dump and Die :)
 */
function vdd() {
    $args = func_get_args();
    call_user_func('var_dump', $args);
    die();
}

function add_app($applicationName, $default = false) {
    add_app_routes($applicationName);
    if ($default) {
        add_app_twig($applicationName, true);
    } else {
        add_app_twig($applicationName);
    }
}