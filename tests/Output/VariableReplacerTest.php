<?php

use LaravelDoctrine\Migrations\Output\VariableReplacer;
use PHPUnit\Framework\TestCase;

class VariableReplacerTest extends TestCase
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
