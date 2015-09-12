<?php

namespace LaravelDoctrine\Migrations\Naming;

use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;
use Illuminate\Support\Str;
use LaravelDoctrine\Migrations\Finders\LaravelNamedMigrationsFinder;

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
        return date('YmdHis') . '_' . $this->str->snake($input, '_');
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

    /**
     * @return MigrationFinderInterface
     */
    public function getFinder()
    {
        return new LaravelNamedMigrationsFinder;
    }
}
