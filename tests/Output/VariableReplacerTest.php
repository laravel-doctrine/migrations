<?php

use LaravelDoctrine\Migrations\Output\VariableReplacer;

class VariableReplacerTest extends PHPUnit_Framework_TestCase
{
    public function test_can_replace_variables()
    {
        $replacer = new VariableReplacer();

        $contents = $replacer->replace(
            '<variable> <variable2>',
            ['<variable>', '<variable2>'],
            ['value', 'value2']
        );

        $this->assertEquals('value value2', $contents);
    }
}
