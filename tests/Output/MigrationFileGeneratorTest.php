<?php

use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Naming\DefaultNamingStrategy;
use LaravelDoctrine\Migrations\Output\FileWriter;
use LaravelDoctrine\Migrations\Output\MigrationFileGenerator;
use LaravelDoctrine\Migrations\Output\StubLocator;
use LaravelDoctrine\Migrations\Output\VariableReplacer;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class MigrationFileGeneratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MigrationFileGenerator
     */
    protected $generator;

    /**
     * @var Mockery\Mock
     */
    protected $configuration;

    protected function setUp(): void
    {
        $this->generator = new MigrationFileGenerator(
            new StubLocator(),
            new VariableReplacer(),
            new FileWriter()
        );

        $this->configuration = m::mock(Configuration::class);
        $this->configuration->shouldReceive('getMigrationsNamespace')->andReturn('Database\Migrations');
        $this->configuration->shouldReceive('getMigrationsDirectory')->andReturn(__DIR__ . '/../stubs/migrations');
        $this->configuration->shouldReceive('getNamingStrategy')->andReturn(new DefaultNamingStrategy());
    }

    public function test_can_generate_blank_migration_file()
    {
        $filename = $this->generator->generate($this->configuration, false, false);

        $this->assertFileWasCreated($filename);
    }

    public function test_can_generate_create_migration_file()
    {
        $filename = $this->generator->generate($this->configuration, 'users', false);

        $this->assertFileWasCreated($filename);
    }

    public function test_can_generate_update_migration_file()
    {
        $filename = $this->generator->generate($this->configuration, false, 'users');

        $this->assertFileWasCreated($filename);
    }

    /**
     * @param $filename
     */
    protected function assertFileWasCreated($filename)
    {
        $this->assertEquals('Version' . date('YmdHis'), $filename);
        $this->assertTrue(strlen(file_get_contents(__DIR__ . '/../stubs/migrations/' . $filename . '.php')) > 0);

        unlink(__DIR__ . '/../stubs/migrations/' . $filename . '.php');
    }
}
