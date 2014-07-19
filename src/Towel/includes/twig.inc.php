<?php

/**
 * Adds a path into the Twig Filesystem Loader.
 *
 * @param $path the path to add
 * @param bool $default If true will send the path to the beginning, this means the most important path
 * @param bool $weight path into a specif place
 *
 * @return boolean.
 */
function add_twig_path($path, $default = false, $weight = false) {
    $app = get_app();

    if ($default === true) {
        $app->getTwigLoader()->prependPath($path);
        return true;
    } elseif ($default === false && $weight === false) {
        $app->getTwigLoader()->addPath($path);
        return true;
    }

    $paths = $app->getTwigLoader()->getPaths();
    $reindexed_paths = array();

    if (count($paths) < $weight) { //Higer than current paths so goes last.
        $app->getTwigLoader()->addPath($path);
        return true;
    }

    if (count($paths) > 0) {
        $reindexed_paths[] = array_shift($paths); //Defaults keeps being the default.
    }

    $index = 0;
    while ($current_path = array_shift($paths)) { // Look a position for the current weight.
        if ($index == $weight) {
            $reindexed_paths[] = $path;
        } else {
            $reindexed_paths[] = $current_path;
        }
        $index++;
    }
    $app->getTwigLoader()->setPaths($reindexed_paths);
    return true;
}

/**
 * Adds an Application Views folder to twig loader.
 *
 * @param $application The name of the application
 * @param bool $default If true will send the path to the beginning, this means the most important application
 * @param bool $weight Set the application path views into a specif place.
 *
 * @return boolean.
 */
function add_app_twig($application, $default = false, $weight = false) {
    $path = APPS_DIR . '/'. $application . '/Views';
    return add_twig_path($path, $default, $weight);
}