<?php

use LaravelDoctrine\Migrations\Output\FileWriter;
use PHPUnit\Framework\TestCase;

class FileWriterTest extends TestCase
{
    public function test_can_write_to_non_existing_path()
    {
        $writer = new FileWriter;

        $this->expectException(InvalidArgumentException::class);
        /**
         * This is hacky, but on TravisCI in PHP 7.2 this expectErrorMessage call was failing
         * for some reason it wasn't found. Since this is a PHPUnit\TestCase object, I felt
         * like this was a safe enough change to make.
         */
        $this->expectExceptionMessage('Migrations directory "doesntexist" does not exist.');

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
