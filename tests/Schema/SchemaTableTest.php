<?php

declare(strict_types=1);

use Doctrine\DBAL\Schema\Column;
use LaravelDoctrine\Migrations\Schema\Table;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class SchemaTableTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Mockery\Mock
     */
    protected $dbal;

    /**
     * @var Table
     */
    protected $table;

    protected function setUp(): void
    {
        $this->dbal  = m::mock(\Doctrine\DBAL\Schema\Table::class);
        $this->table = new Table($this->dbal);
    }

    public function test_guid()
    {
        $this->dbal->shouldReceive('addColumn')->with('guid', 'guid')->once();

        $this->table->guid('guid');
    }

    public function test_primary()
    {
        $this->dbal->shouldReceive('setPrimaryKey')->with(['id'], null)->once();

        $this->table->primary('id');
    }

    public function test_unique()
    {
        $this->dbal->shouldReceive('addUniqueIndex')->with(['email'], null, [])->once();

        $this->table->unique('email');
    }

    public function test_index()
    {
        $this->dbal->shouldReceive('addIndex')->with(['email'], null, [], [])->once();

        $this->table->index('email');
    }

    public function test_foreign()
    {
        $this->dbal->shouldReceive('addForeignKeyConstraint')->with('users', ['user_id'], ['id'], [], null)->once();

        $this->table->foreign('users', 'user_id', 'id');
    }

    public function test_increments()
    {
        $this->dbal->shouldReceive('addColumn')->with(
            'id',
            'integer',
            [
                'autoIncrement' => true,
                'unsigned'      => true
            ]
        )->once();
        $this->dbal->shouldReceive('setPrimaryKey')->with(['id'], null)->once();

        $this->table->increments('id');
    }

    public function test_small_increments()
    {
        $this->dbal->shouldReceive('addColumn')->with(
            'id',
            'smallint',
            [
                'autoIncrement' => true,
                'unsigned'      => true
            ]
        )->once();
        $this->dbal->shouldReceive('setPrimaryKey')->with(['id'], null)->once();

        $this->table->smallIncrements('id');
    }

    public function test_big_increments()
    {
        $this->dbal->shouldReceive('addColumn')->with(
            'id',
            'bigint',
            [
                'autoIncrement' => true,
                'unsigned'      => true
            ]
        )->once();
        $this->dbal->shouldReceive('setPrimaryKey')->with(['id'], null)->once();

        $this->table->bigIncrements('id');
    }

    public function test_string()
    {
        $this->dbal->shouldReceive('addColumn')->with('string', 'string', ['length' => 255])->once();
        $this->table->string('string');
    }

    public function test_text()
    {
        $this->dbal->shouldReceive('addColumn')->with('text', 'text')->once();
        $this->table->text('text');
    }

    public function test_integer()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'integer', [
            'autoIncrement' => false,
            'unsigned'      => false
        ])->once();
        $this->table->integer('column');

        $this->dbal->shouldReceive('addColumn')->with('column', 'integer', [
            'autoIncrement' => true,
            'unsigned'      => true
        ])->once();
        $this->table->integer('column', true, true);
    }

    public function test_small_integer()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'smallint', [
            'autoIncrement' => false,
            'unsigned'      => false
        ])->once();
        $this->table->smallInteger('column');

        $this->dbal->shouldReceive('addColumn')->with('column', 'smallint', [
            'autoIncrement' => true,
            'unsigned'      => true
        ])->once();
        $this->table->smallInteger('column', true, true);
    }

    public function test_big_integer()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'bigint', [
            'autoIncrement' => false,
            'unsigned'      => false
        ])->once();
        $this->table->bigInteger('column');

        $this->dbal->shouldReceive('addColumn')->with('column', 'bigint', [
            'autoIncrement' => true,
            'unsigned'      => true
        ])->once();
        $this->table->bigInteger('column', true, true);
    }

    public function test_unsigned_smallint()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'smallint', [
            'autoIncrement' => false,
            'unsigned'      => true
        ])->once();
        $this->table->unsignedSmallInteger('column');
    }

    public function test_unsigned_integer()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'integer', [
            'autoIncrement' => false,
            'unsigned'      => true
        ])->once();
        $this->table->unsignedInteger('column');
    }

    public function test_unsigned_bigint()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'bigint', [
            'autoIncrement' => false,
            'unsigned'      => true
        ])->once();
        $this->table->unsignedBigInteger('column');
    }

    public function test_float()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'float', [
            'precision' => 8,
            'scale'     => 2
        ])->once();
        $this->table->float('column');
    }

    public function test_decimal()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'decimal', [
            'precision' => 8,
            'scale'     => 2
        ])->once();
        $this->table->decimal('column');
    }

    public function test_boolean()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'boolean')->once();
        $this->table->boolean('column');
    }

    public function test_json()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'json')->once();
        $this->table->json('column');
    }

    public function test_date()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'date')->once();
        $this->table->date('column');
    }

    public function test_dateTime()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'datetime')->once();
        $this->table->dateTime('column');
    }

    public function test_dateTimeTz()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'datetimetz')->once();
        $this->table->dateTimeTz('column');
    }

    public function test_timestamp()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'datetime')->once();
        $this->table->timestamp('column');
    }

    public function test_timestampTz()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'datetimetz')->once();
        $this->table->timestampTz('column');
    }

    public function test_nullable_timestamps()
    {
        $column = m::mock(Column::class);
        $this->dbal->shouldReceive('addColumn')->with('created_at', 'datetime')->andReturn($column)->once();
        $this->dbal->shouldReceive('addColumn')->with('updated_at', 'datetime')->andReturn($column)->once();
        $column->shouldReceive('setNotnull')->with(false)->twice();
        $this->table->nullableTimestamps();
    }

    public function test_timestamps()
    {
        $this->dbal->shouldReceive('addColumn')->with('created_at', 'datetime')->once();
        $this->dbal->shouldReceive('addColumn')->with('updated_at', 'datetime')->once();
        $this->table->timestamps();
    }

    public function test_timestampsTz()
    {
        $this->dbal->shouldReceive('addColumn')->with('created_at', 'datetimetz')->once();
        $this->dbal->shouldReceive('addColumn')->with('updated_at', 'datetimetz')->once();
        $this->table->timestampsTz();
    }

    public function test_softdeletes()
    {
        $column = m::mock(Column::class);
        $this->dbal->shouldReceive('addColumn')->with('deleted_at', 'datetime')->andReturn($column)->once();
        $column->shouldReceive('setNotnull')->with(false)->once();
        $this->table->softDeletes();
    }

    public function test_binary()
    {
        $column = m::mock(Column::class);
        $this->dbal->shouldReceive('addColumn')->with('column', 'binary', ['length' => 255])->andReturn($column)->once();
        $column->shouldReceive('setNotnull')->with(false)->once();
        $this->table->binary('column');
    }

    public function test_remember_token()
    {
        $column = m::mock(Column::class);
        $this->dbal->shouldReceive('addColumn')->with('remember_token', 'string', ['length' => 100])
                   ->andReturn($column)->once();
        $column->shouldReceive('setNotnull')->with(false)->once();
        $this->table->rememberToken('column');
    }

    public function test_get_dbal_table()
    {
        $this->assertInstanceOf(\Doctrine\DBAL\Schema\Table::class, $this->table->getTable());
    }

    public function test_drop_column()
    {
        $this->dbal->shouldReceive('dropColumn')->with('column')->once();

        $this->table->dropColumn('column');
    }
}
