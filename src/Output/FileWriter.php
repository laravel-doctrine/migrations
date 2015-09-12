<?php

namespace LaravelDoctrine\Migrations\Output;

use InvalidArgumentException;

class FileWriter
{
    /**
     * @param string $contents
     * @param string $filename
     * @param string $directory
     *
     * @return int
     */
    public function write($contents, $filename, $directory)
    {
        $path = rtrim($directory, '/') . '/' . $filename . '.php';

        if (!file_exists($directory)) {
            throw new InvalidArgumentException(sprintf('Migrations directory "%s" does not exist.', $directory));
        }

        return file_put_contents($path, $contents);
    }
}
