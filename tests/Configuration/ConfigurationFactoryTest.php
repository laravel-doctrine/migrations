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

        $this->config->shouldReceive('get')
            ->once()
            ->with('migrations.default', [])
            ->andReturn([
                'name' => 'Doctrine Migrations',
                'namespace' => 'Database\\Migrations',
                'table' => 'migrations',
                'schema.filter' => '/^(?).*$/',
                'directory' => database_path('migrations'),
                'naming_strategy' => DefaultNamingStrategy::class,
            ])
        ;

        $this->configuration->shouldReceive('setFilterSchemaAssetsExpression')->with('/^(?).*$/')->once();

        $this->container->shouldReceive('make')
            ->with(DefaultNamingStrategy::class)
            ->once()
            ->andReturn(new DefaultNamingStrategy())
        ;

        $configuration = $this->factory->make($this->connection);

        $this->assertInstanceOf(Configuration::class, $configuration);
        $this->assertEquals('Doctrine Migrations', $configuration->getName());
        $this->assertEquals('Database\\Migrations', $configuration->getMigrationsNamespace());
        $this->assertEquals('migrations', $configuration->getMigrationsTableName());
        $this->assertInstanceOf(DefaultNamingStrategy::class, $configuration->getNamingStrategy());
        $this->assertEquals(database_path('migrations'), $configuration->getMigrationsDirectory());
    }

    public function test_can_make_configuration_for_custom_entity_manager()
    {
        $this->connection->shouldReceive('getConfiguration')->andReturn($this->configuration);

        $this->config->shouldReceive('has')
            ->once()
            ->with('migrations.custom_entity_manager')
            ->andReturn(true)
        ;
        $this->config->shouldReceive('get')
            ->once()
            ->with('migrations.custom_entity_manager', [])
            ->andReturn([
                'name' => 'Migrations',
                'namespace' => 'Database\\Migrations\\Custom',
                'table' => 'migrations',
                'schema.filter' => '/^(?).*$/',
                'directory' => database_path('migrations/custom'),
                'naming_strategy' => DefaultNamingStrategy::class,
            ])
        ;

        $this->configuration->shouldReceive('setFilterSchemaAssetsExpression')->with('/^(?).*$/')->once();
        $this->container->shouldReceive('make')
            ->with(DefaultNamingStrategy::class)
            ->once()
            ->andReturn(new DefaultNamingStrategy())
        ;

        $configuration = $this->factory->make($this->connection, 'custom_entity_manager');

        $this->assertInstanceOf(Configuration::class, $configuration);
        $this->assertEquals('Migrations', $configuration->getName());
        $this->assertEquals('Database\\Migrations\\Custom', $configuration->getMigrationsNamespace());
        $this->assertEquals('migrations', $configuration->getMigrationsTableName());
        $this->assertInstanceOf(DefaultNamingStrategy::class, $configuration->getNamingStrategy());
        $this->assertEquals(database_path('migrations/custom'), $configuration->getMigrationsDirectory());
    }

    public function test_returns_default_configuration_if_not_defined()
    {
        $this->connection->shouldReceive('getConfiguration')->andReturn($this->configuration);

        $this->config->shouldReceive('has')
            ->once()
            ->with('migrations.custom_entity_manager')
            ->andReturn(false)
        ;
        $this->config->shouldReceive('get')
            ->once()
            ->with('migrations.default', [])
            ->andReturn([
                'name' => 'Doctrine Migrations',
                'namespace' => 'Database\\Migrations',
                'table' => 'migrations',
                'schema.filter' => '/^(?).*$/',
                'directory' => database_path('migrations'),
                'naming_strategy' => DefaultNamingStrategy::class,
            ])
        ;

        $this->configuration->shouldReceive('setFilterSchemaAssetsExpression')->with('/^(?).*$/')->once();
        $this->container->shouldReceive('make')
            ->with(DefaultNamingStrategy::class)
            ->once()
            ->andReturn(new DefaultNamingStrategy())
        ;

        $configuration = $this->factory->make($this->connection, 'custom_entity_manager');

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

/**
 * @param string $path
 */
function database_path($path)
{
    return __DIR__ . '/../stubs/' . $path;
}
