<?php

use LaravelDoctrine\Migrations\Output\FileWriter;
use PHPUnit\Framework\TestCase;

class FileWriterTest extends TestCase
{
    public function test_can_write_to_non_existing_path()
    {
        $writer = new FileWriter;

        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Migrations directory "doesntexist" does not exist.');

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
