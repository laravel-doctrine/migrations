<?php

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
    public function locate($stub)
    {
        $this->location = $stub;

        return $this;
    }

    /**
     * @return string
     */
    public function get()
    {
        return file_get_contents($this->getLocation());
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
}
