<?php

namespace Towel\Tests;

class TowelTest extends \PHPUnit_Framework_TestCase {

    public function testConfiguration()
    {
        global $appConfig;
        $this->assertEquals(empty($appConfig), false, 'AppConfig is not available');
    }

    public function testGetApp() {
        $towel = get_app();
        $this->assertInstanceOf('\Towel\BaseApp', $towel, 'Is not an instance of Towel');
    }

    public function testGetBaseController() {
        $baseController = get_base_controller();
        $this->assertInstanceOf('\Towel\Controller\BaseController', $baseController, 'Is not the base controller of Towel');
    }

    public function testGetInstance() {
        //Test function get Instance
        $towel = get_instance('app', array(), true);
        $towel2 = get_instance('app', array(), true);
        $this->assertTrue(($towel === $towel2), 'get_instance is not returning singletons');

        //Test the wrapper in Towel
        $towel3 = $towel->getInstance('app', array(), true);
        $this->assertTrue(($towel === $towel3), '$towel-getInstance() is not returning singletons');

        //Test if is reading the config
        $config = $towel->config();
        $this->assertInstanceOf($config['class_map']['app'], $towel, 'get instance and config have different instances');

        //Test non singletons
        $towel4 = $towel->getInstance('app');
        $this->assertTrue(($towel == $towel4), 'get_instance same object but the same instance');
        $this->assertFalse(($towel === $towel4), 'get_instance is not returning different instances');
    }

    public function testAddApplication() {
        //Test Add any app
        add_app('Frontend');
        $paths = get_app()->getTwigLoader()->getPaths();
        $this->assertEquals(2, count($paths), 'Must be 2 paths in twigs paths');
        $this->assertEquals($paths[1], APPS_DIR . '/Frontend/Views');

        //Reseting twitgs, leaving only towel twig
        get_app()->getTwigLoader()->setPaths(array($paths[0]));

        //Adding frontend as default
        add_app('Frontend', true);
        $paths2 = get_app()->getTwigLoader()->getPaths();
        $this->assertEquals(2, count($paths2), 'Must be 2 paths in twigs paths');
        $this->assertEquals($paths2[0], APPS_DIR . '/Frontend/Views', 'Frontend must be first');
        $this->assertEquals($paths2[1], APP_ROOT_DIR . '/vendor/42mate/towel/src/Towel/Views', 'Towel twigs must be in the 2 item');
    }

    public function testTowelGetApps() {
        $apps = \Towel\Towel::getApps();
        $this->assertTrue(is_array($apps), 'getApps is not an array');
        $this->assertTrue(count($apps) > 2, 'There should be at least one app');
        foreach($apps as $app) {
            $this->assertTrue(count($app) == 2, 'Each item in apps must have two items');
            $this->assertTrue(file_exists($app['path']), 'Item path must exists');
            $this->assertTrue(is_dir($app['path']), 'Item path must be a directory');
        }
    }
}