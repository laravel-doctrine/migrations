<?php

namespace LaravelDoctrine\Migrations\Output;

use LaravelDoctrine\Migrations\Configuration\Configuration;

class MigrationFileGenerator
{
    /**
     * @var string
     */
    protected $stub = 'stubs/blank.stub';

    /**
     * @var array
     */
    protected $variables = [
        '<namespace>',
        '<class>',
        '<up>',
        '<down>'
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
     * @param Configuration $configuration
     *
     * @return string
     */
    public function generate($name, Configuration $configuration)
    {
        $contents = $this->locator->locate($this->stub)->get();

        $contents = $this->replacer->replace($contents, $this->variables, [
            $configuration->getMigrationsNamespace(),
            $configuration->getNamingStrategy()->getClassName($name)
        ]);

        $filename = $configuration->getNamingStrategy()->getFilename($name);

        $this->writer->write(
            $contents,
            $filename,
            $configuration->getMigrationsDirectory()
        );

        return $filename;
    }
}
