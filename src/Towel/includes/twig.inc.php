<?php

/**
 * Add a path or array of paths to the twig loader.
 *
 * @param $path Array or string.
 */
function add_twig_path($path) {
    $app = get_app();
    $app->silex['twig.loader.filesystem']->addPath($path);
}

/**
 * Adds an Application Views folder to twig loader.
 *
 * @param $application The name of the application
 */
function add_app_twig($application) {
    add_twig_path(APPS_DIR . '/'. $application . '/Views');
}