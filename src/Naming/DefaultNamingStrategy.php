<?php

namespace LaravelDoctrine\Migrations\Naming;

use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;
use Doctrine\DBAL\Migrations\Finder\RecursiveRegexFinder;

class DefaultNamingStrategy implements NamingStrategy
{
    /**
     * @return string
     */
    public function getFilename()
    {
        return 'Version' . date('YmdHis');
    }

    /**
     * @return string
     */
    public function getClassName()
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
