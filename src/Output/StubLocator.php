<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Output;

class StubLocator
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @param $stub
     *
     * @return StubLocator
     */
    public function locate($stub): StubLocator
    {
        $this->location = $stub;

        return $this;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return file_get_contents($this->getLocation());
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }
}
