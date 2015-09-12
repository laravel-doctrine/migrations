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
        $this->location = __DIR__ . DIRECTORY_SEPARATOR . $stub;

        return $this;
    }

    /**
     * @return string
     */
    public function get()
    {
        return file_get_contents($this->location);
    }
}
