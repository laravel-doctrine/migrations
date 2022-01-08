<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Schema;

use Closure;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table as Blueprint;
use Doctrine\DBAL\Types\Type;

class Table
{
    /**
     * @var Blueprint
     */
    protected $table;

    /**
     * @param Blueprint $table
     * @param Closure|null $callback
     */
    public function __construct(Blueprint $table, Closure $callback = null)
    {
        $this->table = $table;

        if (!is_null($callback)) {
            $callback($this);
        }
    }

    /**
     * Create a new guid column on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function guid(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::GUID);
    }

    /**
     * Specify the primary key(s) for the table.
     *
     * @param string $columns
     * @param null $name
     *
     * @return Blueprint|null
     */
    public function primary(string $columns, $name = null): ?Blueprint
    {
        $columns = is_array($columns) ? $columns : [$columns];

        return $this->table->setPrimaryKey($columns, $name);
    }

    /**
     * Dropping an index which satisfy the criteria
     *
     * @param string|array $name
     * @param Closure $satisfy
     */
    protected function _dropIndex($name, $satisfy){
        if (is_string($name)) {
            $this->table->dropIndex($name);
        } else {
            $indexes = $this->table->getIndexes();

            $matched = [];
            foreach ($indexes as $key => $index) {
                if ($satisfy($index)) {
                    $columns = $index->getColumns();

                    if (count(array_diff($columns, $name)) == 0 && count(array_diff($name, $columns)) == 0) {
                        array_push($matched, $key);
                    }
                }
            }

            foreach ($matched as $indexName) {
                $this->table->dropIndex($indexName);
            }
        }
    }

    /**
     * Dropping a defined primary index from the table
     *
     * @param string|array $name Name of the primary index or column names associated with the index
     *
     * @return void
     */
    public function dropPrimary($name)
    {
        $this->_dropIndex($name, function(Index $index){
            return $index->isPrimary();
        });
    }

    /**
     * Specify a unique index for the table.
     *
     * @param string|array $columns
     * @param string       $name
     * @param array        $options
     *
     * @return Blueprint|null
     */
    public function unique($columns, $name = null, $options = []): ?Blueprint
    {
        $columns = is_array($columns) ? $columns : [$columns];

        return $this->table->addUniqueIndex($columns, $name, $options);
    }

    /**
     * Dropping a defined unique index from the table
     *
     * @param string|array $name Name of the unique index or column names associated with the index
     *
     * @return void
     */
    public function dropUnique($name)
    {
        $this->_dropIndex($name, function(Index $index){
            return $index->isUnique();
        });
    }

    /**
     * Specify an index for the table.
     *
     * @param string|array $columns
     * @param string       $name
     * @param array        $flags
     * @param array        $options
     *
     * @return Blueprint|null
     */
    public function index($columns, $name = null, $flags = [], $options = []): ?Blueprint
    {
        $columns = is_array($columns) ? $columns : [$columns];

        return $this->table->addIndex($columns, $name, $flags, $options);
    }

    /**
     * Dropping a basic index from the table
     *
     * @param string|array $name Name of the primary index or column names associated with the index
     *
     * @return void
     */
    public function dropIndex($name)
    {
        $this->_dropIndex($name, function(Index $index){
            return true;
        });
    }

    /**
     * Specify a foreign key for the table.
     *
     * @param string $table
     * @param array|string $localColumnNames
     * @param array|string $foreignColumnNames
     * @param array $options
     * @param null $constraintName
     *
     * @return Blueprint|null
     */
    public function foreign(
        string $table,
        $localColumnNames,
        $foreignColumnNames = 'id',
        $options = [],
        $constraintName = null
    ): ?Blueprint {
        $localColumnNames   = is_array($localColumnNames) ? $localColumnNames : [$localColumnNames];
        $foreignColumnNames = is_array($foreignColumnNames) ? $foreignColumnNames : [$foreignColumnNames];

        return $this->table->addForeignKeyConstraint(
            $table,
            $localColumnNames,
            $foreignColumnNames,
            $options,
            $constraintName
        );
    }

    /**
     * Dropping a foreign key from the table
     *
     * @param string|array $name Name of the foreign index or column names associated with the index
     */
    public function dropForeign($name)
    {
        $this->_dropIndex($name, function(Index $index){
            return !$index->isUnique()&&!$index->isPrimary();
        });
    }

    /**
     * Create a new auto-incrementing integer (4-byte) column on the table.
     *
     * @param string $columnName
     *
     * @return Column|null
     */
    public function increments(string $columnName): ?Column
    {
        $column = $this->integer($columnName, true, true);
        $this->primary($columnName);

        return $column;
    }

    /**
     * Create a new auto-incrementing small integer (2-byte) column on the table.
     *
     * @param string $columnName
     *
     * @return Column|null
     */
    public function smallIncrements(string $columnName): ?Column
    {
        $column = $this->smallInteger($columnName, true, true);
        $this->primary($columnName);

        return $column;
    }

    /**
     * Create a new auto-incrementing big integer (8-byte) column on the table.
     *
     * @param string $columnName
     *
     * @return Column|null
     */
    public function bigIncrements(string $columnName): ?Column
    {
        $column = $this->bigInteger($columnName, true, true);
        $this->primary($columnName);

        return $column;
    }

    /**
     * Create a new string column on the table.
     *
     * @param string $column
     * @param int $length
     *
     * @return Column|null
     */
    public function string(string $column, $length = 255): ?Column
    {
        return $this->table->addColumn($column, Type::STRING, compact('length'));
    }

    /**
     * Create a new text column on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function text(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::TEXT);
    }

    /**
     * Create a new integer (4-byte) column on the table.
     *
     * @param string $column
     * @param bool $autoIncrement
     * @param bool $unsigned
     *
     * @return Column|null
     */
    public function integer(string $column, $autoIncrement = false, $unsigned = false): ?Column
    {
        return $this->table->addColumn($column, Type::INTEGER, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new small integer (2-byte) column on the table.
     *
     * @param string $column
     * @param bool $autoIncrement
     * @param bool $unsigned
     *
     * @return Column|null
     */
    public function smallInteger(string $column, $autoIncrement = false, $unsigned = false): ?Column
    {
        return $this->table->addColumn($column, Type::SMALLINT, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new big integer (8-byte) column on the table.
     *
     * @param string $column
     * @param bool $autoIncrement
     * @param bool $unsigned
     *
     * @return Column|null
     */
    public function bigInteger(string $column, $autoIncrement = false, $unsigned = false): ?Column
    {
        return $this->table->addColumn($column, Type::BIGINT, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new unsigned small integer (2-byte) column on the table.
     *
     * @param string $column
     * @param bool $autoIncrement
     *
     * @return Column|null
     */
    public function unsignedSmallInteger(string $column, $autoIncrement = false): ?Column
    {
        return $this->smallInteger($column, $autoIncrement, true);
    }

    /**
     * Create a new unsigned integer (4-byte) column on the table.
     *
     * @param string $column
     * @param bool $autoIncrement
     *
     * @return Column|null
     */
    public function unsignedInteger(string $column, $autoIncrement = false): ?Column
    {
        return $this->integer($column, $autoIncrement, true);
    }

    /**
     * Create a new unsigned big integer (8-byte) column on the table.
     *
     * @param string $column
     * @param bool $autoIncrement
     *
     * @return Column|null
     */
    public function unsignedBigInteger(string $column, $autoIncrement = false): ?Column
    {
        return $this->bigInteger($column, $autoIncrement, true);
    }

    /**
     * Create a new float column on the table.
     *
     * @param string $column
     * @param int $precision
     * @param int $scale
     *
     * @return Column|null
     */
    public function float(string $column, $precision = 8, $scale = 2): ?Column
    {
        return $this->table->addColumn($column, Type::FLOAT, compact('precision', 'scale'));
    }

    /**
     * Create a new decimal column on the table.
     *
     * @param string $column
     * @param int $precision
     * @param int $scale
     *
     * @return Column|null
     */
    public function decimal(string $column, $precision = 8, $scale = 2): ?Column
    {
        return $this->table->addColumn($column, Type::DECIMAL, compact('precision', 'scale'));
    }

    /**
     * Create a new boolean column on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function boolean(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::BOOLEAN);
    }

    /**
     * Create a new json column on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function json(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::JSON_ARRAY);
    }

    /**
     * Create a new date column on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function date(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::DATE);
    }

    /**
     * Create a new date-time column on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function dateTime(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::DATETIME);
    }

    /**
     * Create a new date-time column (with time zone) on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function dateTimeTz(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::DATETIMETZ);
    }

    /**
     * Create a new time column on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function time(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::TIME);
    }

    /**
     * Create a new timestamp column on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function timestamp(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::DATETIME);
    }

    /**
     * Create a new timestamp (with time zone) column on the table.
     *
     * @param string $column
     *
     * @return Column|null
     */
    public function timestampTz(string $column): ?Column
    {
        return $this->table->addColumn($column, Type::DATETIMETZ);
    }

    /**
     * Add nullable creation and update timestamps to the table.
     * @return void
     */
    public function nullableTimestamps()
    {
        $this->timestamp('created_at')->setNotnull(false);

        $this->timestamp('updated_at')->setNotnull(false);
    }

    /**
     * Add creation and update timestamps to the table.
     * @return void
     */
    public function timestamps()
    {
        $this->timestamp('created_at');

        $this->timestamp('updated_at');
    }

    /**
     * Add creation and update timestampTz columns to the table.
     * @return void
     */
    public function timestampsTz()
    {
        $this->timestampTz('created_at');

        $this->timestampTz('updated_at');
    }

    /**
     * Add a "deleted at" timestamp for the table.
     *
     * @return Column|null
     */
    public function softDeletes(): ?Column
    {
        return $this->timestamp('deleted_at')->setNotnull(false);
    }

    /**
     * Create a new binary column on the table.
     *
     * @param string $column
     * @param int $length
     * @return Column|null
     */
    public function binary(string $column, $length = 255): ?Column
    {
        return $this->table->addColumn($column, Type::BINARY, compact('length'))->setNotnull(false);
    }

    /**
     * Adds the `remember_token` column to the table.
     *
     * @return Column|null
     */
    public function rememberToken(): ?Column
    {
        return $this->string('remember_token', 100)->setNotnull(false);
    }

    /**
     * @return Blueprint
     */
    public function getTable(): Blueprint
    {
        return $this->table;
    }

    /**
     * @param $column
     *
     * @return Blueprint|null
     */
    public function dropColumn($column): ?Blueprint
    {
        return $this->table->dropColumn($column);
    }
}
