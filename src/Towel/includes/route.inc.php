<?php

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
    $towel = get_app();

    if (!empty($options['controller']) && !empty($options['action'])) {
        $action = array(
            $options['controller'], $options['action']
        );
    } else {
        throw new Exception('Needs an action');
    }

    $route = $towel->silex()->$method($route, function (\Symfony\Component\HttpFoundation\Request $request) use ($action, $options) {
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
 * Returns a full url with a given path.
 *
 * @param $route
 * @param array $parameters
 * @param bool $absolute
 *
 * @return string : Url.
 */
function url($route, $parameters = array(), $absolute = false)
{
    if ($route[0] == '/') { //Literal route, checks if need to include a prefix.
        $url_prefix = '';
        if (count(APP_BASE_URL) > 0) {
            $url_prefix = APP_BASE_URL;
        }
        return $url_prefix . $route;
    }

    $towel = get_app(); //Named route, will generate the url.
    if ($absolute) {
        $return = $towel->url($route, $parameters);
    } else {
        $return = $towel->path($route, $parameters);
    }

    return $return;
}

/**
 * Returns the Url for an upload images
 *
 * @param $pic : Path from the upload dir.
 * @return string : Full url to the image if exists, empty if not.
 *
 */
function url_image($pic)
{
    if (!empty($pic) && file_exists(APP_UPLOADS_DIR . '/' . $pic)) {
        return APP_BASE_URL . 'uploads/' . $pic;
    }
    return '';
}

/**
 * Includes the routes of the application.
 *
 * @param String $application_name
 *
 * @throws Exception
 */
function add_app_routes($application_name) {

    $app_dir = APPS_DIR . "/$application_name/Routes";

    if (!file_exists($app_dir)) {
        throw new Exception('App does not exists');
    }

    foreach (glob($app_dir . "/*Routes.php") as $routeFile) {
        require_once "$routeFile";
    }
}