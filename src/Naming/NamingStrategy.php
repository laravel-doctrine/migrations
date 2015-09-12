<?php

namespace LaravelDoctrine\Migrations\Naming;

use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;

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

    /**
     * @return MigrationFinderInterface
     */
    public function getFinder();
}
