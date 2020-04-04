<?php

namespace LaravelDoctrine\Migrations\Naming;

use Doctrine\Migrations\Finder\MigrationFinder;

interface NamingStrategy
{
    /**
     * @param int|null $version
     *
     * @return string
     */
    public function getFilename($version = null);

    /**
     * @param int|null $version
     *
     * @return string
     */
    public function getClassName($version = null);

    /**
     * @return MigrationFinder
     */
    public function getFinder();
}
