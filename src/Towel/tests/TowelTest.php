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


}