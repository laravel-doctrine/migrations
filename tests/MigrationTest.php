<?php

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\MigrationRepository;
use Doctrine\Migrations\OutputWriter;
use Doctrine\Migrations\Stopwatch;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Exceptions\ExecutedUnavailableMigrationsException;
use LaravelDoctrine\Migrations\Exceptions\MigrationVersionException;
use LaravelDoctrine\Migrations\Migration;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mockery\Mock
     */
    protected $configuration;

    /**
     * @var Mockery\Mock
     */
    protected $dependencyFactory;

    /**
     * @var Mockery\Mock
     */
    protected $migrationRepository;

    /**
     * @var Mockery\Mock
     */
    protected $outputWriter;

    /**
     * @var Mockery\Mock
     */
    protected $stopwatch;

    protected function setUp(): void
    {
        $this->configuration = m::mock(Configuration::class);
        $this->dependencyFactory = m::mock(DependencyFactory::class);
        $this->migrationRepository = m::mock(MigrationRepository::class);
        $this->outputWriter = m::mock(OutputWriter::class);
        $this->stopwatch = m::mock(Stopwatch::class);
        $this->configuration->shouldReceive('getOutputWriter')->andReturn($this->outputWriter);
        $this->configuration->shouldReceive('getDependencyFactory')->andReturn($this->dependencyFactory);
        $this->dependencyFactory->shouldReceive('getMigrationRepository')->andReturn($this->migrationRepository);
        $this->dependencyFactory->shouldReceive('getStopwatch')->andReturn($this->stopwatch);
    }

	/**
	 * @throws ExecutedUnavailableMigrationsException
	 */
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

        $this->assertInstanceOf(\Doctrine\Migrations\Migrator::class, $migration->getMigration());
        $this->assertEquals('version3', $migration->getVersion());
    }

	/**
	 * @throws ExecutedUnavailableMigrationsException
	 */
	public function test_throw_exception_when_executed_unavailable_migrations()
    {
        $this->configuration->shouldReceive('resolveVersionAlias')->andReturn('version3');
        $this->configuration->shouldReceive('getMigratedVersions')->andReturn([
            'version1'
        ]);

        $this->configuration->shouldReceive('getAvailableVersions')->andReturn([

        ]);

        $this->expectException(ExecutedUnavailableMigrationsException::class);

        $migration = new Migration(
            $this->configuration,
            'latest'
        );

        $migration->checkIfNotExecutedUnavailableMigrations();
    }

	/**
	 * @throws ExecutedUnavailableMigrationsException
	 */
	public function test_throw_exception_when_no_version()
    {
        $this->configuration->shouldReceive('resolveVersionAlias')->andReturn(null);
        $this->configuration->shouldReceive('getMigratedVersions')->andReturn([
            'version1'
        ]);

        $this->configuration->shouldReceive('getAvailableVersions')->andReturn([
            'version1'
        ]);

        $this->expectException(MigrationVersionException::class);

        $migration = new Migration(
            $this->configuration,
            'latest'
        );
    }
}
