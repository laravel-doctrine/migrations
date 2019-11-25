<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Schema;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Output\SqlBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class SqlBuilderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mockery\Mock
     */
    protected $configuration;

    /**
     * @var Mockery\Mock
     */
    protected $from;

    /**
     * @var Mockery\Mock
     */
    protected $to;

    /**
     * @var Mockery\Mock
     */
    protected $connection;

    /**
     * @var SqlBuilder
     */
    protected $builder;

    /**
     * @var Mockery\Mock
     */
    protected $platform;

    protected function setUp(): void
    {
        $this->configuration = m::mock(Configuration::class);
        $this->connection    = m::mock(Connection::class);
        $this->platform      = m::mock(AbstractPlatform::class);
        $this->from          = m::mock(Schema::class);
        $this->to            = m::mock(Schema::class);

        $this->builder = new SqlBuilder;

        $this->configuration->shouldReceive('getConnection')->andReturn($this->connection);
        $this->connection->shouldReceive('getDatabasePlatform')->andReturn($this->platform);
        $this->platform->shouldReceive('getName')->andReturn('mysql');

        $this->configuration->shouldReceive('getMigrationsTableName')->andReturn('migrations');
    }

    public function test_can_build_up_sql()
    {
        $this->from->shouldReceive('getMigrateToSql')
                   ->with($this->to, $this->platform)
                   ->andReturn([
                       'QUERY1',
                       'QUERY2'
                   ]);

        $sql = $this->builder->up($this->configuration, $this->from, $this->to);

        $this->assertEquals(trim(file_get_contents(__DIR__ . '/../stubs/up.stub')), $sql);
    }

    public function test_can_build_down_sql()
    {
        $this->from->shouldReceive('getMigrateFromSql')
                   ->with($this->to, $this->platform)
                   ->andReturn([
                       'QUERY3',
                       'QUERY4'
                   ]);

        $sql = $this->builder->down($this->configuration, $this->from, $this->to);

        $this->assertEquals(trim(file_get_contents(__DIR__ . '/../stubs/down.stub')), $sql);
    }
}
