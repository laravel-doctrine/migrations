<?php

namespace LaravelDoctrine\Migrations\Naming;

use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;

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
     * @return MigrationFinderInterface
     */
    public function getFinder();
}
