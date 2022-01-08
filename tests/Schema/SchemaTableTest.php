<?php

declare(strict_types=1);

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\Type;
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
        $this->dbal->shouldReceive('addColumn')->with('guid', 'guid');

        $this->table->guid('guid');
    }

    public function test_primary()
    {
        $this->dbal->shouldReceive('setPrimaryKey')->with(['id'], null);

        $this->table->primary('id');
    }

    public function test_unique()
    {
        $this->dbal->shouldReceive('addUniqueIndex')->with(['email'], null, []);

        $this->table->unique('email');
    }

    public function test_index()
    {
        $this->dbal->shouldReceive('addIndex')->with(['email'], null, [], []);

        $this->table->index('email');
    }

    public function test_foreign()
    {
        $this->dbal->shouldReceive('addForeignKeyConstraint')->with('users', ['user_id'], ['id'], [], null);

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
        );
        $this->dbal->shouldReceive('setPrimaryKey')->with(['id'], null);

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
        );
        $this->dbal->shouldReceive('setPrimaryKey')->with(['id'], null);

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
        );
        $this->dbal->shouldReceive('setPrimaryKey')->with(['id'], null);

        $this->table->bigIncrements('id');
    }

    public function test_string()
    {
        $this->dbal->shouldReceive('addColumn')->with('string', 'string', ['length' => 255]);
        $this->table->string('string');
    }

    public function test_text()
    {
        $this->dbal->shouldReceive('addColumn')->with('text', 'text');
        $this->table->text('text');
    }

    public function test_integer()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'integer', [
            'autoIncrement' => false,
            'unsigned'      => false
        ]);
        $this->table->integer('column');

        $this->dbal->shouldReceive('addColumn')->with('column', 'integer', [
            'autoIncrement' => true,
            'unsigned'      => true
        ]);
        $this->table->integer('column', true, true);
    }

    public function test_small_integer()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'smallint', [
            'autoIncrement' => false,
            'unsigned'      => false
        ]);
        $this->table->smallInteger('column');

        $this->dbal->shouldReceive('addColumn')->with('column', 'smallint', [
            'autoIncrement' => true,
            'unsigned'      => true
        ]);
        $this->table->smallInteger('column', true, true);
    }

    public function test_big_integer()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'bigint', [
            'autoIncrement' => false,
            'unsigned'      => false
        ]);
        $this->table->bigInteger('column');

        $this->dbal->shouldReceive('addColumn')->with('column', 'bigint', [
            'autoIncrement' => true,
            'unsigned'      => true
        ]);
        $this->table->bigInteger('column', true, true);
    }

    public function test_unsigned_smallint()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'smallint', [
            'autoIncrement' => false,
            'unsigned'      => true
        ]);
        $this->table->unsignedSmallInteger('column');
    }

    public function test_unsigned_integer()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'integer', [
            'autoIncrement' => false,
            'unsigned'      => true
        ]);
        $this->table->unsignedInteger('column');
    }

    public function test_unsigned_bigint()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'bigint', [
            'autoIncrement' => false,
            'unsigned'      => true
        ]);
        $this->table->unsignedBigInteger('column');
    }

    public function test_float()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'float', [
            'precision' => 8,
            'scale'     => 2
        ]);
        $this->table->float('column');
    }

    public function test_decimal()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'decimal', [
            'precision' => 8,
            'scale'     => 2
        ]);
        $this->table->decimal('column');
    }

    public function test_boolean()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'boolean');
        $this->table->boolean('column');
    }

    public function test_json()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'json_array');
        $this->table->json('column');
    }

    public function test_date()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'date');
        $this->table->date('column');
    }

    public function test_dateTime()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'datetime');
        $this->table->dateTime('column');
    }

    public function test_dateTimeTz()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'datetimetz');
        $this->table->dateTimeTz('column');
    }

    public function test_timestamp()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'datetime');
        $this->table->timestamp('column');
    }

    public function test_timestampTz()
    {
        $this->dbal->shouldReceive('addColumn')->with('column', 'datetimetz');
        $this->table->timestampTz('column');
    }

    public function test_nullable_timestamps()
    {
        $column = m::mock(Column::class);
        $this->dbal->shouldReceive('addColumn')->with('created_at', 'datetime')->andReturn($column);
        $this->dbal->shouldReceive('addColumn')->with('updated_at', 'datetime')->andReturn($column);
        $column->shouldReceive('setNotnull')->with(false)->twice();
        $this->table->nullableTimestamps();
    }

    public function test_timestamps()
    {
        $this->dbal->shouldReceive('addColumn')->with('created_at', 'datetime');
        $this->dbal->shouldReceive('addColumn')->with('updated_at', 'datetime');
        $this->table->timestamps();
    }

    public function test_timestampsTz()
    {
        $this->dbal->shouldReceive('addColumn')->with('created_at', 'datetimetz');
        $this->dbal->shouldReceive('addColumn')->with('updated_at', 'datetimetz');
        $this->table->timestampsTz();
    }

    public function test_softdeletes()
    {
        $column = m::mock(Column::class);
        $this->dbal->shouldReceive('addColumn')->with('deleted_at', 'datetime')->andReturn($column);
        $column->shouldReceive('setNotnull')->with(false)->once();
        $this->table->softDeletes();
    }

    public function test_binary()
    {
        $column = m::mock(Column::class);
        $this->dbal->shouldReceive('addColumn')->with('column', 'binary', ['length' => 255])->andReturn($column);
        $column->shouldReceive('setNotnull')->with(false)->once();
        $this->table->binary('column');
    }

    public function test_remember_token()
    {
        $column = m::mock(Column::class);
        $this->dbal->shouldReceive('addColumn')->with('remember_token', 'string', ['length' => 100])
                   ->andReturn($column);
        $column->shouldReceive('setNotnull')->with(false)->once();
        $this->table->rememberToken('column');
    }

    public function test_get_dbal_table()
    {
        $this->assertInstanceOf(\Doctrine\DBAL\Schema\Table::class, $this->table->getTable());
    }

    public function test_drop_column()
    {
        $this->dbal->shouldReceive('dropColumn')->with('column');

        $this->table->dropColumn('column');
    }

    public function test_drop_unique()
    {
        $this->dbal->shouldReceive('dropIndex')->with('unique');

        $this->table->dropForeign('unique');
    }

    public function test_drop_unique_with_exact_column_names()
    {
        $this->dbal->shouldReceive('getIndexes')->andReturn([
            'index1'=> new Index('index1', ['column1', 'column2', 'column3']),
            'index2'=> new Index('index2', ['column1', 'column2']),
            'index3'=> new Index('index3', ['column1']),
            'index4'=> new Index('index4', ['column1', 'column2', 'column3'], true),
            'index5'=> new Index('index5', ['column1', 'column2'], true),
            'index6'=> new Index('index6', ['column1'], true),
            'index7'=> new Index('index7', ['column1', 'column2', 'column3'], true),
            'index8'=> new Index('index8', ['column1', 'column2'], true),
            'index9'=> new Index('index9', ['column1'], true),
        ]);

        $this->dbal->shouldReceive('dropIndex')->with('index5');
        $this->dbal->shouldReceive('dropIndex')->with('index8');

        $this->table->dropUnique(['column1', 'column2']);
    }

    public function test_drop_primary()
    {
        $this->dbal->shouldReceive('dropIndex')->with('primary');

        $this->table->dropPrimary('primary');
    }

    public function test_drop_primary_with_exact_column_names()
    {
        $this->dbal->shouldReceive('getIndexes')->andReturn([
            'index1'=> new Index('index1', ['column1', 'column2', 'column3']),
            'index2'=> new Index('index2', ['column1', 'column2']),
            'index3'=> new Index('index3', ['column1']),
            'index4'=> new Index('index4', ['column1', 'column2', 'column3'], false, true),
            'index5'=> new Index('index5', ['column1', 'column2'], false, true),
            'index6'=> new Index('index6', ['column1'], false, true),
        ]);

        $this->dbal->shouldReceive('dropIndex')->with('index5');

        $this->table->dropPrimary(['column1', 'column2']);
    }

    public function test_drop_foreign()
    {
        $this->dbal->shouldReceive('dropIndex')->with('foreign');

        $this->table->dropForeign('foreign');
    }

    public function test_drop_foreign_with_exact_column_names()
    {
        $this->dbal->shouldReceive('getIndexes')->andReturn([
            'index1'=> new Index('index1', ['column1', 'column2', 'column3'], true),
            'index2'=> new Index('index2', ['column1', 'column2'], true),
            'index3'=> new Index('index3', ['column1'], true),
            'index4'=> new Index('index4', ['column1', 'column2', 'column3'], false, true),
            'index5'=> new Index('index5', ['column1', 'column2'], false, true),
            'index6'=> new Index('index6', ['column1'], false, true),
            'index7'=> new Index('index7', ['column1', 'column2', 'column3']),
            'index8'=> new Index('index8', ['column1', 'column2']),
            'index9'=> new Index('index9', ['column1']),
        ]);

        $this->dbal->shouldReceive('dropIndex')->with('index8');

        $this->table->dropForeign(['column1', 'column2']);
    }

    public function test_drop_index()
    {
        $this->dbal->shouldReceive('dropIndex')->with('index');

        $this->table->dropIndex('index');
    }

    public function test_drop_index_with_exact_column_names()
    {
        $this->dbal->shouldReceive('getIndexes')->andReturn([
            'index1'=> new Index('index1', ['column1', 'column2', 'column3'], true),
            'index2'=> new Index('index2', ['column1', 'column2'], true),
            'index3'=> new Index('index3', ['column1'], true),
            'index4'=> new Index('index4', ['column1', 'column2', 'column3'], false, true),
            'index5'=> new Index('index5', ['column1', 'column2'], false, true),
            'index6'=> new Index('index6', ['column1'], false, true),
            'index7'=> new Index('index7', ['column1', 'column2', 'column3']),
            'index8'=> new Index('index8', ['column1', 'column2']),
            'index9'=> new Index('index9', ['column1']),
        ]);

        $this->dbal->shouldReceive('dropIndex')->with('index2');
        $this->dbal->shouldReceive('dropIndex')->with('index5');
        $this->dbal->shouldReceive('dropIndex')->with('index8');

        $this->table->dropIndex(['column1', 'column2']);
    }
}
