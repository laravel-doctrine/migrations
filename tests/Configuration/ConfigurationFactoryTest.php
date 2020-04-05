<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Configuration\ConfigurationFactory;
use LaravelDoctrine\Migrations\Naming\DefaultNamingStrategy;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ConfigurationFactoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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

    /**
     * @var Mockery\Mock
     */
    protected $schemaManager;

    /**
     * @var Mockery\Mock
     */
    protected $databasePlatform;

    protected function setUp(): void
    {
        $this->container     = m::mock(Container::class);
        $this->config        = m::mock(Repository::class);
        $this->connection    = m::mock(Connection::class);
        $this->configuration = m::mock(\Doctrine\DBAL\Configuration::class);
        $this->schemaManager = m::mock(AbstractSchemaManager::class);
        $this->databasePlatform = m::mock(AbstractPlatform::class);

        $this->factory = new ConfigurationFactory(
            $this->config,
            $this->container
        );
    }

    public function test_can_make_configuration()
    {
        $this->connection->shouldReceive('getConfiguration')->andReturn($this->configuration);
        $this->connection->shouldReceive('getSchemaManager')->andReturn($this->schemaManager);
        $this->connection->shouldReceive('getDatabasePlatform')->andReturn($this->databasePlatform);

        $this->config->shouldReceive('get')
            ->once()
            ->with('migrations.default', [])
            ->andReturn([
                'name'                => 'Doctrine Migrations',
                'namespace'           => 'Database\\Migrations',
                'table'               => 'migrations',
                'schema'              => ['filter' => '/^(?).*$/'],
                'directory'           => database_path('migrations'),
                'organize_migrations' => Configuration::VERSIONS_ORGANIZATION_BY_YEAR_AND_MONTH,
                'naming_strategy'     => DefaultNamingStrategy::class,
            ])
        ;

        $this->configuration->shouldReceive('setFilterSchemaAssetsExpression')->with(m::mustBe('/^(?).*$/'))->once();

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
        $this->assertEquals(true, $configuration->areMigrationsOrganizedByYear());
        $this->assertEquals(true, $configuration->areMigrationsOrganizedByYearAndMonth());
    }

    public function test_can_make_configuration_for_custom_entity_manager()
    {
        $this->connection->shouldReceive('getConfiguration')->andReturn($this->configuration);
        $this->connection->shouldReceive('getSchemaManager')->andReturn($this->schemaManager);
        $this->connection->shouldReceive('getDatabasePlatform')->andReturn($this->databasePlatform);

        $this->config->shouldReceive('has')
            ->once()
            ->with('migrations.custom_entity_manager')
            ->andReturn(true)
        ;
        $this->config->shouldReceive('get')
            ->once()
            ->with('migrations.custom_entity_manager', [])
            ->andReturn([
                'name'                => 'Migrations',
                'namespace'           => 'Database\\Migrations\\Custom',
                'table'               => 'migrations',
                'schema'              => ['filter' => '/^(?!^(custom)$).*$/'],
                'directory'           => database_path('migrations/custom'),
                'organize_migrations' => Configuration::VERSIONS_ORGANIZATION_BY_YEAR_AND_MONTH,
                'naming_strategy'     => DefaultNamingStrategy::class,
            ])
        ;

        $this->configuration->shouldReceive('setFilterSchemaAssetsExpression')
            ->with(m::mustBe('/^(?!^(custom)$).*$/'))
            ->once()
        ;
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
        $this->assertEquals(true, $configuration->areMigrationsOrganizedByYear());
        $this->assertEquals(true, $configuration->areMigrationsOrganizedByYearAndMonth());
    }

    public function test_returns_default_configuration_if_not_defined()
    {
        $this->connection->shouldReceive('getConfiguration')->andReturn($this->configuration);
        $this->connection->shouldReceive('getSchemaManager')->andReturn($this->schemaManager);
        $this->connection->shouldReceive('getDatabasePlatform')->andReturn($this->databasePlatform);

        $this->config->shouldReceive('has')
            ->once()
            ->with('migrations.custom_entity_manager')
            ->andReturn(false)
        ;
        $this->config->shouldReceive('get')
            ->once()
            ->with('migrations.default', [])
            ->andReturn([
                'name'                => 'Doctrine Migrations',
                'namespace'           => 'Database\\Migrations',
                'table'               => 'migrations',
                'schema'              => ['filter' => '/^(?).*$/'],
                'directory'           => database_path('migrations'),
                'organize_migrations' => Configuration::VERSIONS_ORGANIZATION_BY_YEAR_AND_MONTH,
                'naming_strategy'     => DefaultNamingStrategy::class,
            ])
        ;

        $this->configuration->shouldReceive('setFilterSchemaAssetsExpression')->with(m::mustBe('/^(?).*$/'))->once();
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
        $this->assertEquals(true, $configuration->areMigrationsOrganizedByYear());
        $this->assertEquals(true, $configuration->areMigrationsOrganizedByYearAndMonth());
    }
}

/**
 * @param string $path
 */
function database_path($path)
{
    return __DIR__ . '/../stubs/' . $path;
}
