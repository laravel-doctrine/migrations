<?php

namespace LaravelDoctrine\Migrations\Naming;

use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;
use Doctrine\DBAL\Migrations\Finder\RecursiveRegexFinder;

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

    /**
     * @return MigrationFinderInterface
     */
    public function getFinder()
    {
        return new RecursiveRegexFinder;
    }
}
