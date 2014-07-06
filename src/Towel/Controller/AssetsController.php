<?php

namespace Towel\Controller;

use Symfony\Component\HttpFoundation\Response;

class AssetsController extends BaseController {

    static $validExtensions = array(
        'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'bmp'
    );

    /**
     * Serves assets from the Application directory.
     *
     * To use this controller in your template use the function asset_url to get a valid url
     * for this controller.
     *
     * @param $request
     * @return string|Response
     */
    public function index($request) {
        $application = $request->get('application');
        $path = $request->get('path');

        if (empty($application) || empty($path)) {
            return new Response(
                "Not a valid asset",
                404
            );
        }

        if (strpos('..', $path) === 0) {
            return new Response(
                "Only assets will be served",
                404
            );
        }

        if ($application !== 'Towel') {
            $asset_path = APP_ROOT_DIR . '/Application/' . $application . '/Views/assets/' . $path;
        } else {
            $asset_path = APP_FW_DIR. '/Views/assets/' . $path;
        }

        if (!file_exists($asset_path)) {
            return new Response(
                "Asset does not exists",
                404
            );
        }

        $asset_type = mime_content_type($asset_path);
        $asset_content = file_get_contents($asset_path);
        $asset_info = pathinfo($asset_path);

        if (!empty($asset_info['extension']) && in_array($asset_info['extension'], self::$validExtensions)) {
            return new Response(
                $asset_content,
                200,
                ['Content-Type' => $asset_type]
            );
        }

        return new Response(
            "Not a valid extension",
            404
        );
    }

}