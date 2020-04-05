<?php

use Doctrine\Migrations\Finder\RecursiveRegexFinder;
use LaravelDoctrine\Migrations\Naming\DefaultNamingStrategy;
use PHPUnit\Framework\TestCase;

class DefaultNamingStrategyTest extends TestCase
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
