<?php

/**
 * Returns a full url with a given path.
 *
 * @param $path
 *
 * @return string : Url.
 */
function url($path) {
    return APP_BASE_URL . $path;
}

/**
 * Returns the Url for an upload images
 *
 * @param $pic : Path from the upload dir.
 * @return string : Full url to the image if exists, empty if not.
 *
 */
function url_image($pic) {
    if (!empty($pic) && file_exists(APP_UPLOADS_DIR . '/' . $pic)) {
        return APP_BASE_URL . 'uploads/' . $pic;
    }
    return '';
}