<?php

namespace LaravelDoctrine\Migrations\Output;

use LaravelDoctrine\Migrations\Configuration\Configuration;

class MigrationFileGenerator
{
    /**
     * @var array
     */
    protected $variables = [
        '<namespace>',
        '<class>',
        '<table>'
    ];

    /**
     * @var StubLocator
     */
    protected $locator;

    /**
     * @var VariableReplacer
     */
    protected $replacer;

    /**
     * @var FileWriter
     */
    protected $writer;

    /**
     * @param StubLocator      $locator
     * @param VariableReplacer $replacer
     * @param FileWriter       $writer
     */
    public function __construct(StubLocator $locator, VariableReplacer $replacer, FileWriter $writer)
    {
        $this->locator  = $locator;
        $this->replacer = $replacer;
        $this->writer   = $writer;
    }

    /**
     * @param string        $name
     * @param bool|string   $create
     * @param bool|string   $update
     * @param Configuration $configuration
     *
     * @return string
     */
    public function generate($name, $create = false, $update = false, Configuration $configuration)
    {
        $stub = $this->getStub($create, $update);

        $contents = $this->locator->locate($stub)->get();

        $contents = $this->replacer->replace($contents, $this->variables, [
            $configuration->getMigrationsNamespace(),
            $configuration->getNamingStrategy()->getClassName($name),
            $this->getTableName($create, $update)
        ]);

        $filename = $configuration->getNamingStrategy()->getFilename($name);

        $this->writer->write(
            $contents,
            $filename,
            $configuration->getMigrationsDirectory()
        );

        return $filename;
    }

    /**
     * @param bool|string $create
     * @param bool|string $update
     *
     * @return string
     */
    protected function getStub($create, $update)
    {
        $stub = 'blank';
        if ($create) {
            $stub = 'create';
        }

        if ($update) {
            $stub = 'update';
        }

        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $stub . '.stub';
    }

    /**
     * @param bool|string $create
     * @param bool|string $update
     *
     * @return null
     */
    protected function getTableName($create, $update)
    {
        if ($create) {
            return $create;
        }

        if ($update) {
            return $update;
        }
    }
}
