<?php

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use LaravelDoctrine\Migrations\Configuration\ConfigurationFactory;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ConfigurationProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ConfigurationProvider
     */
    protected $provider;

    /**
     * @var Mockery\Mock
     */
    protected $registry;

    /**
     * @var Mockery\Mock
     */
    protected $factory;

    /**
     * @var Mockery\Mock
     */
    protected $connection;

    protected function setUp(): void
    {
        $this->registry   = m::mock(ManagerRegistry::class);
        $this->factory    = m::mock(ConfigurationFactory::class);
        $this->connection = m::mock(Connection::class);

        $this->provider = new ConfigurationProvider(
            $this->registry,
            $this->factory
        );
    }

    public function test_can_get_configuration_for_default_connection()
    {
        $this->registry->shouldReceive('getConnection')
                        ->with(null)
                        ->andReturn($this->connection);

        $this->factory->shouldReceive('make')
                        ->with($this->connection, null)
                        ->andReturn('configuration');

        $this->assertEquals('configuration', $this->provider->getForConnection());
    }

    public function test_can_get_configuration_for_specific_connection()
    {
        $this->registry->shouldReceive('getConnection')
                        ->with('connection')
                        ->andReturn($this->connection);

        $this->factory->shouldReceive('make')
                        ->with($this->connection, 'connection')
                        ->andReturn('configuration');

        $this->assertEquals('configuration', $this->provider->getForConnection('connection'));
    }
}
