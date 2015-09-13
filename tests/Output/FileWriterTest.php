<?php

use LaravelDoctrine\Migrations\Output\FileWriter;

class FileWriterTest extends PHPUnit_Framework_TestCase
{
    public function test_can_write_to_non_existing_path()
    {
        $writer = new FileWriter;

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Migrations directory "doesntexist" does not exist.'
        );

        $writer->write('contents', 'filename.php', 'doesntexist');
    }

    public function test_can_write()
    {
        $writer = new FileWriter;

        $writer->write('contents', 'filename', __DIR__ . '/../stubs/migrations');

        $this->assertEquals('contents', file_get_contents(__DIR__ . '/../stubs/migrations/filename.php'));

        // delete old files
        unlink(__DIR__ . '/../stubs/migrations/filename.php');
    }
}
