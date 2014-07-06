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