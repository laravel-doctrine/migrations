<?php

use LaravelDoctrine\Migrations\Output\StubLocator;

class StubLocatorTest extends PHPUnit_Framework_TestCase
{
    public function test_can_get_a_stub()
    {
        $location = __DIR__ . '/../stubs/migration.stub';
        $locator  = (new StubLocator)->locate($location);

        $this->assertEquals($location, $locator->getLocation());
        $this->assertEquals('migration', trim($locator->get()));
    }
}
