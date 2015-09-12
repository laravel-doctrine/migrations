<?php

namespace LaravelDoctrine\Migrations\Naming;

interface NamingStrategy
{
    /**
     * @param string $input
     *
     * @return string
     */
    public function getFilename($input);

    /**
     * @param string $input
     *
     * @return string
     */
    public function getClassName($input);
}
