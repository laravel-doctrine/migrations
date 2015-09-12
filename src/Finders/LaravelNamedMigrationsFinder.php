<?php

namespace LaravelDoctrine\Migrations\Finders;

use Doctrine\DBAL\Migrations\Finder\AbstractFinder;
use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;
use Illuminate\Support\Str;

class LaravelNamedMigrationsFinder extends AbstractFinder implements MigrationFinderInterface
{
    /**
     * {@inheritdoc}
     */
    public function findMigrations($directory, $namespace = null)
    {
        $dir = $this->getRealPath($directory);

        return $this->loadMigrations($this->getMatches($this->createIterator($dir)), $namespace);
    }

    /**
     * Create a recursive iterator to find all the migrations in the subdirectories.
     *
     * @param $dir
     *
     * @return \RegexIterator
     */
    private function createIterator($dir)
    {
        return new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            ),
            $this->getPattern(),
            \RegexIterator::GET_MATCH
        );
    }

    private function getPattern()
    {
        return sprintf('#^.+\\%s[^\\%s]{1,255}\\.php$#i', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
    }

    /**
     * Transform the recursiveIterator result array of array into the expected array of migration file
     *
     * @param $iteratorFilesMatch
     *
     * @return array
     */
    private function getMatches($iteratorFilesMatch)
    {
        $files = [];
        foreach ($iteratorFilesMatch as $file) {
            $files[] = $file[0];
        }

        return $files;
    }

    /**
     * Load the migrations and return an array of thoses loaded migrations
     *
     * @param $files     array of migration filename found
     * @param $namespace namespace of thoses migrations
     *
     * @return array constructed with the migration name as key and the value is the fully qualified name of the migration
     */
    protected function loadMigrations($files, $namespace)
    {
        $migrations = [];
        foreach ($files as $file) {
            static::requireOnce($file);
            $migrations[$this->getVersion($file)] = $this->getClassName($namespace, $file);
        }

        return $migrations;
    }

    /**
     * @param $namespace
     * @param $file
     *
     * @return string
     */
    protected function getClassName($namespace, $file)
    {
        $fileName  = basename($file, '.php');
        $className = implode('_', array_slice(explode('_', $fileName), 1));
        $className = Str::studly($className);

        return sprintf('%s\\%s', $namespace, $className);
    }

    /**
     * @param $file
     *
     * @return string
     */
    protected function getVersion($file)
    {
        $fileName = basename($file, '.php');

        return implode('_', array_slice(explode('_', $fileName), 0, 1));
    }
}
