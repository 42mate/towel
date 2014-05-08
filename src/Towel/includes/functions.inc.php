<?php

/**
 * Yes, this is a generic global function files. As dirty as it sounds you'll love it.
 *
 * Do not use this file to create specific logic, just for general propose functions.
 */

function generatePassword($length = 9, $strength = 0)
{
    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEUY";
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }

    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $password;
}

/**
 * Ads a route in the application.
 *
 * @param $method : GET, POST, PUT, DELETE
 * @param $route : The route Pattern (Any Silex Route Valid Pattern).
 * @param $action : Array with an instance of controller and his action or just an string with
 *                  the function name to call.
 * @param $options: Array of Options Key, Value
 *
 *    secure : Boolean if you require and authorized user for the action.
 *
 * @return The action Result.
 */
function add_route($method, $route, $action, $options = array())
{
    global $app;
    $app->$method($route, function (\Symfony\Component\HttpFoundation\Request $request) use ($action, $options) {
        $controller = get_base_controller();

        if (!empty($options['secure']) && $options['secure'] == true) {
            if (!$controller->isAuthenticated()) {
                return $controller->redirect('/login');
            }
        }

        return call_user_func_array($action, array($request));
    });
}

/**
 * Returns the App.
 *
 * @return \Towel\BaseApp
 */
function get_app()
{
    return new \Towel\BaseApp();
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