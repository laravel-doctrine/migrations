<?php

namespace LaravelDoctrine\Migrations\Naming;

use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;

interface NamingStrategy
{
    /**
     * @return string
     */
    public function getFilename();

    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return MigrationFinderInterface
     */
    public function getFinder();
}
