<?php

namespace Towel\Tests;

class TowelTest extends \PHPUnit_Framework_TestCase {

    public function testConfiguration()
    {
        global $appConfig;
        $this->assertEquals(empty($appConfig), false, 'AppConfig is not available');
    }


}