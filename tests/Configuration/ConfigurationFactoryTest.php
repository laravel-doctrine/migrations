<?php

use Doctrine\DBAL\Connection;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Configuration\ConfigurationFactory;
use LaravelDoctrine\Migrations\Naming\DefaultNamingStrategy;
use Mockery as m;

class ConfigurationFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigurationFactory
     */
    protected $factory;

    /**
     * @var Mockery\Mock
     */
    protected $container;

    /**
     * @var Mockery\Mock
     */
    protected $config;

    /**
     * @var Mockery\Mock
     */
    protected $connection;

    /**
     * @var Mockery\Mock
     */
    protected $configuration;

    protected function setUp()
    {
        $this->container     = m::mock(Container::class);
        $this->config        = m::mock(Repository::class);
        $this->connection    = m::mock(Connection::class);
        $this->configuration = m::mock(\Doctrine\DBAL\Configuration::class);

        $this->factory = new ConfigurationFactory(
            $this->config,
            $this->container
        );
    }

    public function test_can_make_configuration()
    {
        $this->connection->shouldReceive('getConfiguration')->andReturn($this->configuration);

        $this->config->shouldReceive('get')->once()
                     ->with('migrations.name', 'Doctrine Migrations')
                     ->andReturn('Doctrine Migrations');
        $this->config->shouldReceive('get')->once()
                     ->with('migrations.namespace', 'Database\\Migrations')
                     ->andReturn('Database\\Migrations');
        $this->config->shouldReceive('get')->once()
                     ->with('migrations.table', 'migrations')
                     ->andReturn('migrations');

        $this->config->shouldReceive('get')->once()
                     ->with('migrations.schema.filter', '/^(?).*$/')
                     ->andReturn('migrations');
        $this->configuration->shouldReceive('setFilterSchemaAssetsExpression')
                            ->with('/^(?).*$/')->once();

        $this->config->shouldReceive('get')->once()
                     ->with('migrations.naming_strategy', DefaultNamingStrategy::class)
                     ->andReturn(DefaultNamingStrategy::class);
        $this->container->shouldReceive('make')->with(DefaultNamingStrategy::class)
                        ->once()
                        ->andReturn(new DefaultNamingStrategy());

        $this->config->shouldReceive('get')->once()
                     ->with('migrations.directory', database_path('migrations'))
                     ->andReturn(database_path('migrations'));

        $configuration = $this->factory->make($this->connection);

        $this->assertInstanceOf(Configuration::class, $configuration);
        $this->assertEquals('Doctrine Migrations', $configuration->getName());
        $this->assertEquals('Database\\Migrations', $configuration->getMigrationsNamespace());
        $this->assertEquals('migrations', $configuration->getMigrationsTableName());
        $this->assertInstanceOf(DefaultNamingStrategy::class, $configuration->getNamingStrategy());
        $this->assertEquals(database_path('migrations'), $configuration->getMigrationsDirectory());
    }

    protected function tearDown()
    {
        m::close();
    }
}

function database_path($path)
{
    return __DIR__ . '/../stubs/' . $path;
}
