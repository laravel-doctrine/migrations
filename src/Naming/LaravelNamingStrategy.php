<?php

namespace LaravelDoctrine\Migrations\Naming;

use Illuminate\Support\Str;

class LaravelNamingStrategy implements NamingStrategy
{
    /**
     * @var Str
     */
    protected $str;

    /**
     * @param Str $str
     */
    public function __construct(Str $str)
    {
        $this->str = $str;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public function getFilename($input)
    {
        return date('Y_m_d_His') . '_' . $this->str->snake($input, '_');
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public function getClassName($input)
    {
        return studly_case($input);
    }
}
