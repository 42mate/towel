<?php

/**
 * Yes, this is a generic global function files. As dirty as it sounds you'll love it.
 *
 * Do not use this file to create specific logic, just for general propose functions.
 */

/**
 * Generates a random passoword.
 *
 * @param int $length
 * @param int $strength
 *
 * @return string
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
 * @param array $options :
 *    controller: The controller with the action
 *    action : The method or function name to execute.
 *    secure : Boolean if you require and authorized user for the action.
 *    route_name : Name of the route to be used in the url generator.
 *
 * @throws Exception if no action is provided in options.
 *
 * @return The action Result.
 */
function add_route($method, $route, $options = array())
{
    global $silex;

    if (!empty($options['controller']) && !empty($options['action'])) {
        $action = array(
            $options['controller'], $options['action']
        );
    } else {
        throw new Exception('Needs an action');
    }

    $route = $silex->$method($route, function (\Symfony\Component\HttpFoundation\Request $request) use ($action, $options) {
        $controller = get_base_controller();

        if (!empty($options['secure']) && $options['secure'] == true) {
            if (!$controller->isAuthenticated()) {
                return $controller->redirect('/login');
            }
        }

        return call_user_func_array($action, array($request));
    });

    if (!empty($options['route_name'])) {
        $route->bind($options['route_name']);
    }

}

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