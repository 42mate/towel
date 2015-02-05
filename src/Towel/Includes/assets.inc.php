<?php

/**
 * Returns an Assets valid url to be served with the assets controller.
 *
 * @param $application : Application Name, Case sensitive
 * @param $path : The relative path from the assets folder, for example css/frontend.css
 *
 * @return Url.
 */
function assets_url($application, $path) {
    return '/assets?application=' . $application . '&path=' . $path;
}

/**
 * Adds values into the js settings register.
 * @param $values
 */
function add_js_settings($values) {
    if (!isset($_GLOBALS['towel_js_settings'])) {
        $GLOBALS['towel_js_settings'] = array();
    }
    if (is_array($values)) {
        $GLOBALS['towel_js_settings'] = array_merge($GLOBALS['towel_js_settings'], $values);
    }
}

/**
 * Gets the string of js Settings values.
 *
 * @return String : JS script code with a global variable Towel and the settings inside.
 */
function js_settings() {
    if (!isset($GLOBALS['towel_js_settings'])) {
        $GLOBALS['towel_js_settings'] = array();
    }

    $out = 'Towel = {};';
    $out .= 'Towel.settings = ' . json_encode($GLOBALS['towel_js_settings']) . ';';
    $GLOBALS['towel_js_settings'] = array(); //Clean the settings.
    return $out;
}