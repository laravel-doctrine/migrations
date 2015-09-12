<?php

namespace LaravelDoctrine\Migrations\Naming;

class DefaultNamingStrategy implements NamingStrategy
{
    /**
     * @param string $input
     *
     * @return string
     */
    public function getFilename($input)
    {
        return 'Version' . date('YmdHis');
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public function getClassName($input)
    {
        return 'Version' . date('YmdHis');
    }
}
