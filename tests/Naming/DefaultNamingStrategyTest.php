<?php

use Doctrine\DBAL\Migrations\Finder\RecursiveRegexFinder;
use LaravelDoctrine\Migrations\Naming\DefaultNamingStrategy;

class DefaultNamingStrategyTest extends PHPUnit_Framework_TestCase
{
    public function test_can_get_class_name()
    {
        $this->assertEquals('Version' . date('YmdHis'), (new DefaultNamingStrategy)->getClassName());
    }

    public function test_can_get_filename()
    {
        $this->assertEquals('Version' . date('YmdHis'), (new DefaultNamingStrategy)->getFilename());
    }

    public function test_can_get_finder()
    {
        $this->assertInstanceOf(RecursiveRegexFinder::class, (new DefaultNamingStrategy)->getFinder());
    }
}
