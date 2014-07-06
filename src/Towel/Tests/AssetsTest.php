<?php

namespace Towel\Tests;

use Symfony\Component\HttpFoundation\Request;
use Towel\Controller\AssetsController;

class AssetsTest extends \PHPUnit_Framework_TestCase {

    public function testAssetsUrl() {
        $url = assets_url('Towel', 'js/towel.js');
        $this->assertEquals($url, '/assets?application=Towel&path=js/towel.js');
        $url = assets_url('Towel', 'css/towel.css');
        $this->assertEquals($url, '/assets?application=Towel&path=css/towel.css');
    }

    public function testAssetsController() {
        $assetsControler = new AssetsController();
        $request = new Request();
        $req = $request->create('/assets?application=Towel&path=css/towel_test.css', 'GET');
        $response = $assetsControler->index($req);
        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals('body { font-size: 20px; }', $response->getContent());

        $req = $request->create('/assets?application=Towel&path=css/Error.css', 'GET');
        $response = $assetsControler->index($req);
        $this->assertEquals('404', $response->getStatusCode());

    }
}
