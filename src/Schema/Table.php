<?php

namespace LaravelDoctrine\Migrations\Schema;

use Closure;
use Doctrine\DBAL\Schema\Column;
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
     * @param Closure   $callback
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
     * @param int    $length
     *
     * @return Column
     */
    public function guid($column)
    {
        return $this->table->addColumn($column, Type::GUID);
    }

    /**
     * Specify the primary key(s) for the table.
     *
     * @param string $columns
     * @param string $name
     *
     * @return Blueprint
     */
    public function primary($columns, $name = null)
    {
        $columns = is_array($columns) ? $columns : [$columns];

        return $this->table->setPrimaryKey($columns, $name);
    }

    /**
     * Specify a unique index for the table.
     *
     * @param string|array $columns
     * @param string       $name
     * @param array        $options
     *
     * @return Blueprint
     */
    public function unique($columns, $name = null, $options = [])
    {
        $columns = is_array($columns) ? $columns : [$columns];

        return $this->table->addUniqueIndex($columns, $name, $options);
    }

    /**
     * Specify an index for the table.
     *
     * @param string|array $columns
     * @param string       $name
     * @param array        $flags
     * @param array        $options
     *
     * @return Blueprint
     */
    public function index($columns, $name = null, $flags = [], $options = [])
    {
        $columns = is_array($columns) ? $columns : [$columns];

        return $this->table->addIndex($columns, $name, $flags, $options);
    }

    /**
     * Specify a foreign key for the table.
     *
     * @param string       $table
     * @param array|string $localColumnNames
     * @param array|string $foreignColumnNames
     * @param array        $options
     * @param null         $constraintName
     *
     * @return Blueprint
     */
    public function foreign(
        $table,
        $localColumnNames,
        $foreignColumnNames = 'id',
        $options = [],
        $constraintName = null
    ) {
        $localColumnNames   = is_array($localColumnNames) ? $localColumnNames : [$localColumnNames];
        $foreignColumnNames = is_array($foreignColumnNames) ? $foreignColumnNames : [$foreignColumnNames];

        return $this->table->addForeignKeyConstraint($table, $localColumnNames, $foreignColumnNames, $options,
            $constraintName);
    }

    /**
     * Create a new auto-incrementing integer (4-byte) column on the table.
     *
     * @param string $columnName
     *
     * @return Column
     */
    public function increments($columnName)
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
     * @return Column
     */
    public function smallIncrements($columnName)
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
     * @return Column
     */
    public function bigIncrements($columnName)
    {
        $column = $this->bigInteger($columnName, true, true);
        $this->primary($columnName);

        return $column;
    }

    /**
     * Create a new string column on the table.
     *
     * @param string $column
     * @param int    $length
     *
     * @return Column
     */
    public function string($column, $length = 255)
    {
        return $this->table->addColumn($column, Type::STRING, compact('length'));
    }

    /**
     * Create a new text column on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function text($column)
    {
        return $this->table->addColumn($column, Type::TEXT);
    }

    /**
     * Create a new integer (4-byte) column on the table.
     *
     * @param string $column
     * @param bool   $autoIncrement
     * @param bool   $unsigned
     *
     * @return Column
     */
    public function integer($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->table->addColumn($column, Type::INTEGER, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new small integer (2-byte) column on the table.
     *
     * @param string $column
     * @param bool   $autoIncrement
     * @param bool   $unsigned
     *
     * @return Column
     */
    public function smallInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->table->addColumn($column, Type::SMALLINT, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new big integer (8-byte) column on the table.
     *
     * @param string $column
     * @param bool   $autoIncrement
     * @param bool   $unsigned
     *
     * @return Column
     */
    public function bigInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->table->addColumn($column, Type::BIGINT, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new unsigned small integer (2-byte) column on the table.
     *
     * @param string $column
     * @param bool   $autoIncrement
     *
     * @return Column
     */
    public function unsignedSmallInteger($column, $autoIncrement = false)
    {
        return $this->smallInteger($column, $autoIncrement, true);
    }

    /**
     * Create a new unsigned integer (4-byte) column on the table.
     *
     * @param string $column
     * @param bool   $autoIncrement
     *
     * @return Column
     */
    public function unsignedInteger($column, $autoIncrement = false)
    {
        return $this->integer($column, $autoIncrement, true);
    }

    /**
     * Create a new unsigned big integer (8-byte) column on the table.
     *
     * @param string $column
     * @param bool   $autoIncrement
     *
     * @return Column
     */
    public function unsignedBigInteger($column, $autoIncrement = false)
    {
        return $this->bigInteger($column, $autoIncrement, true);
    }

    /**
     * Create a new float column on the table.
     *
     * @param string $column
     * @param int    $precision
     * @param int    $scale
     *
     * @return Column
     */
    public function float($column, $precision = 8, $scale = 2)
    {
        return $this->table->addColumn($column, Type::FLOAT, compact('precision', 'scale'));
    }

    /**
     * Create a new decimal column on the table.
     *
     * @param string $column
     * @param int    $precision
     * @param int    $scale
     *
     * @return Column
     */
    public function decimal($column, $precision = 8, $scale = 2)
    {
        return $this->table->addColumn($column, Type::DECIMAL, compact('precision', 'scale'));
    }

    /**
     * Create a new boolean column on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function boolean($column)
    {
        return $this->table->addColumn($column, Type::BOOLEAN);
    }

    /**
     * Create a new json column on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function json($column)
    {
        return $this->table->addColumn($column, Type::JSON_ARRAY);
    }

    /**
     * Create a new date column on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function date($column)
    {
        return $this->table->addColumn($column, Type::DATE);
    }

    /**
     * Create a new date-time column on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function dateTime($column)
    {
        return $this->table->addColumn($column, Type::DATETIME);
    }

    /**
     * Create a new date-time column (with time zone) on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function dateTimeTz($column)
    {
        return $this->table->addColumn($column, Type::DATETIMETZ);
    }

    /**
     * Create a new time column on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function time($column)
    {
        return $this->table->addColumn($column, Type::TIME);
    }

    /**
     * Create a new timestamp column on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function timestamp($column)
    {
        return $this->table->addColumn($column, Type::DATETIME);
    }

    /**
     * Create a new timestamp (with time zone) column on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function timestampTz($column)
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
     * @return Column
     */
    public function softDeletes()
    {
        return $this->timestamp('deleted_at')->setNotnull(false);
    }

    /**
     * Create a new binary column on the table.
     *
     * @param string $column
     *
     * @return Column
     */
    public function binary($column, $length = 255)
    {
        return $this->table->addColumn($column, Type::BINARY, compact('length'))->setNotnull(false);
    }

    /**
     * Adds the `remember_token` column to the table.
     * @return Column
     */
    public function rememberToken()
    {
        return $this->string('remember_token', 100)->setNotnull(false);
    }

    /**
     * @return Blueprint
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param $column
     *
     * @return Blueprint
     */
    public function dropColumn($column)
    {
        return $this->table->dropColumn($column);
    }
}
