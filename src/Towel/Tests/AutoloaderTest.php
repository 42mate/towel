<?php

namespace Towel\Tests;

class AutoloaderTest extends \PHPUnit_Framework_TestCase {

    public function __construct() {
        $this->vendorDir = realpath(dirname(__FILE__) . '/../../../../..');
        $this->baseDir = dirname($this->vendorDir);
    }

    /**
     * Tests if Application namespace is in the autoloader and if the path
     * is the baseDir.
     */
    public function testApplicationNamespace() {
        $this->assertTrue(file_exists($this->vendorDir . '/composer/autoload_namespaces.php'), 'Autoload Namespace does not exists, have you run composer ?');
        $namespaces = include $this->vendorDir . '/composer/autoload_namespaces.php';

        $this->assertTrue(isset($namespaces['Application']), 'Application is not a namespaces');
        $this->assertEquals($namespaces['Application'][0], $this->baseDir . '/', 'The path of Application namespace is not the baseDir');
    }

}