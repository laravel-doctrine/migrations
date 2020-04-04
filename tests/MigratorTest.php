<?php

use Doctrine\Migrations\Exception\MigrationException;
use Doctrine\Migrations\MigratorConfiguration;
use Doctrine\Migrations\Version\Version;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Migration;
use LaravelDoctrine\Migrations\Migrator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class MigratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mockery\Mock
     */
    protected $configuration;

    /**
     * @var Mockery\Mock
     */
    protected $migration;

    /**
     * @var Mockery\Mock
     */
    protected $dbalMig;

    protected function setUp(): void
    {
        $this->configuration = m::mock(Configuration::class);
        $this->configuration->shouldReceive('getOutputWriter');

        $this->dbalMig   = m::mock(\Doctrine\Migrations\Migrator::class);
        $this->migration = m::mock(Migration::class);
    }

	/**
	 * @throws MigrationException
	 */
	public function test_migrate()
    {
        $doctrineConfig = new MigratorConfiguration();
        $doctrineConfig->setDryRun(false);
        $doctrineConfig->setTimeAllQueries(false);
        $doctrineConfig->setNoMigrationException(false);

        $this->migration->shouldReceive('getMigration')->andReturn($this->dbalMig);
        $this->migration->shouldReceive('getConfiguration')->andReturn($this->configuration);
        $this->configuration->shouldReceive('getVersion')->andReturn(m::mock(Version::class));
        $this->migration->shouldReceive('getVersion')->andReturn('version1');
        $this->dbalMig->shouldReceive('migrate')->with('version1', with(Mockery::on(function($arg) use ($doctrineConfig) {
            return $arg == $doctrineConfig;
        })))->andReturn([
            'version1' => 'SQL'
        ]);

        $migrator = (new Migrator);
        $migrator->migrate($this->migration);

        $this->assertContains('<info>Migrated:</info> version1', $migrator->getNotes());
    }
}
