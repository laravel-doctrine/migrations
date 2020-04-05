<?php

namespace LaravelDoctrine\Migrations\Naming;

use Doctrine\Migrations\Finder\MigrationDeepFinder;
use Doctrine\Migrations\Finder\MigrationFinder;
use Doctrine\Migrations\Finder\RecursiveRegexFinder;

class DefaultNamingStrategy implements NamingStrategy
{
    /**
     * @return string|string
     */
    public function getFilename($version = null)
    {
        $version = $version ?: date('YmdHis');

        return 'Version' . $version;
    }

    /**
     * @param string|null $version
     *
     * @return string
     */
    public function getClassName($version = null)
    {
        $version = $version ?: date('YmdHis');

        return 'Version' . $version;
    }

    /**
     * @return MigrationDeepFinder
     */
    public function getFinder()
    {
        return new RecursiveRegexFinder;
    }
}
