<?php

use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Exceptions\ExecutedUnavailableMigrationsException;
use LaravelDoctrine\Migrations\Exceptions\MigrationVersionException;
use LaravelDoctrine\Migrations\Migration;
use Mockery as m;

class MigrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mockery\Mock
     */
    protected $configuration;

    protected function setUp()
    {
        $this->configuration = m::mock(Configuration::class);
        $this->configuration->shouldReceive('getOutputWriter');
    }

    public function test_can_make_migration()
    {
        $this->configuration->shouldReceive('resolveVersionAlias')->andReturn('version3');
        $this->configuration->shouldReceive('getMigratedVersions')->andReturn([
            'version1'
        ]);

        $this->configuration->shouldReceive('getAvailableVersions')->andReturn([
            'version1',
            'version2',
            'version3'
        ]);

        $migration = new Migration(
            $this->configuration,
            'latest'
        );

        $this->assertInstanceOf(\Doctrine\DBAL\Migrations\Migration::class, $migration->getMigration());
        $this->assertEquals('version3', $migration->getVersion());
    }

    public function test_throw_exception_when_executed_unavailable_migrations()
    {
        $this->configuration->shouldReceive('resolveVersionAlias')->andReturn('version3');
        $this->configuration->shouldReceive('getMigratedVersions')->andReturn([
            'version1'
        ]);

        $this->configuration->shouldReceive('getAvailableVersions')->andReturn([

        ]);

        $this->setExpectedException(ExecutedUnavailableMigrationsException::class);

        $migration = new Migration(
            $this->configuration,
            'latest'
        );

        $migration->checkIfNotExecutedUnavailableMigrations();
    }

    public function test_throw_exception_when_no_version()
    {
        $this->configuration->shouldReceive('resolveVersionAlias')->andReturn(null);
        $this->configuration->shouldReceive('getMigratedVersions')->andReturn([
            'version1'
        ]);

        $this->configuration->shouldReceive('getAvailableVersions')->andReturn([
            'version1'
        ]);

        $this->setExpectedException(MigrationVersionException::class);

        $migration = new Migration(
            $this->configuration,
            'latest'
        );
    }

    protected function tearDown()
    {
        m::close();
    }
}
