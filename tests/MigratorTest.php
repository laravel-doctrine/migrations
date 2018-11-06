<?php

use Doctrine\DBAL\Migrations\Version;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Migration;
use LaravelDoctrine\Migrations\Migrator;
use Mockery as m;

class MigratorTest extends PHPUnit_Framework_TestCase
{
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

    protected function setUp()
    {
        $this->configuration = m::mock(Configuration::class);
        $this->configuration->shouldReceive('getOutputWriter');

        $this->dbalMig   = m::mock(\Doctrine\DBAL\Migrations\Migration::class);
        $this->migration = m::mock(Migration::class);
    }

    public function test_migrate()
    {
        $this->migration->shouldReceive('getMigration')->andReturn($this->dbalMig);
        $this->migration->shouldReceive('getConfiguration')->andReturn($this->configuration);
        $this->configuration->shouldReceive('getVersion')->andReturn(m::mock(Version::class));
        $this->migration->shouldReceive('getVersion')->andReturn('version1');
        $this->dbalMig->shouldReceive('migrate')->with('version1', false, false)->andReturn([
            'version1' => 'SQL'
        ]);
        $this->dbalMig->shouldReceive('setNoMigrationException')->with(false);

        $migrator = (new Migrator);
        $migrator->migrate($this->migration);

        $this->assertContains('<info>Migrated:</info> version1', $migrator->getNotes());
    }

    protected function tearDown()
    {
        m::close();
    }
}
