<?php

use Doctrine\DBAL\Schema\Schema;
use LaravelDoctrine\Migrations\Schema\Builder;
use LaravelDoctrine\Migrations\Schema\Table;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class SchemaBuilderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mockery\Mock
     */
    protected $schema;

    /**
     * @var Builder
     */
    protected $builder;

    protected function setUp(): void
    {
        $this->schema  = m::mock(Schema::class);
        $this->builder = new Builder($this->schema);
    }

    public function test_create()
    {
        $this->schema->shouldReceive('createTable')
                     ->with('table_name')->once()
                     ->andReturn(m::mock(\Doctrine\DBAL\Schema\Table::class));

        $this->builder->create('table_name', function (Table $table) {
            $this->assertInstanceOf(Table::class, $table);
        });
    }

    public function test_table()
    {
        $this->schema->shouldReceive('getTable')
                     ->with('table_name')->once()
                     ->andReturn(m::mock(\Doctrine\DBAL\Schema\Table::class));

        $this->builder->table('table_name', function (Table $table) {
            $this->assertInstanceOf(Table::class, $table);
        });
    }

    public function test_drop()
    {
        $this->schema->shouldReceive('dropTable')
                     ->with('table_name')->once()
                     ->andReturn('dropped');

        $this->assertEquals('dropped', $this->builder->drop('table_name'));
    }

    public function test_dropIfExists()
    {
        $this->schema->shouldReceive('hasTable')
                     ->with('table_name')->once()
                     ->andReturn(true);

        $this->schema->shouldReceive('dropTable')
                     ->with('table_name')->once()
                     ->andReturn('dropped');

        $this->assertEquals('dropped', $this->builder->dropIfExists('table_name'));
    }

    public function test_rename()
    {
        $this->schema->shouldReceive('renameTable')
                     ->with('table_name', 'tablename')->once()
                     ->andReturn('renamed');

        $this->assertEquals('renamed', $this->builder->rename('table_name', 'tablename'));
    }

    public function test_hasTable()
    {
        $this->schema->shouldReceive('hasTable')
                     ->with('table_name')->once()
                     ->andReturn(true);

        $this->assertTrue($this->builder->hasTable('table_name'));
    }

    public function test_hasColumn()
    {
        $table = m::mock(\Doctrine\DBAL\Schema\Table::class);

        $this->schema->shouldReceive('getTable')
                     ->with('table_name')->once()
                     ->andReturn($table);

        $table->shouldReceive('hasColumn')->once()->with('column_name')->andReturn(true);

        $this->assertTrue($this->builder->hasColumn('table_name', 'column_name'));
    }

    public function test_getColumnListing()
    {
        $table = m::mock(\Doctrine\DBAL\Schema\Table::class);

        $this->schema->shouldReceive('getTable')
                     ->with('table_name')->once()
                     ->andReturn($table);

        $table->shouldReceive('getColumns')->once()->andReturn(['column']);

        $this->assertContains('column', $this->builder->getColumnListing('table_name'));
    }

    public function test_has_columns()
    {
        $table = m::mock(\Doctrine\DBAL\Schema\Table::class);

        $this->schema->shouldReceive('getTable')
                     ->with('table_name')->once()
                     ->andReturn($table);

        $table->shouldReceive('getColumns')->once()->andReturn(['column' => 'instance']);

        $this->assertTrue($this->builder->hasColumns('table_name', ['column']));
    }

    public function test_doesnt_have_column()
    {
        $table = m::mock(\Doctrine\DBAL\Schema\Table::class);

        $this->schema->shouldReceive('getTable')
                     ->with('table_name')->once()
                     ->andReturn($table);

        $table->shouldReceive('getColumns')->once()->andReturn(['column' => 'instance']);

        $this->assertFalse($this->builder->hasColumns('table_name', ['column2']));
    }
}
