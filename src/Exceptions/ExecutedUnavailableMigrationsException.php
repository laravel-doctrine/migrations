<?php

namespace LaravelDoctrine\Migrations\Exceptions;

use Exception;

class ExecutedUnavailableMigrationsException extends Exception
{
    /**
     * @var array
     */
    protected $migrations;

    /**
     * @param array $migrations
     */
    public function __construct(array $migrations = [])
    {
        $this->migrations = $migrations;
    }

    /**
     * @return array
     */
    public function getMigrations()
    {
        return $this->migrations;
    }
}
