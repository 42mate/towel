<?php

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