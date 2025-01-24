<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Schema;

use Closure;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table as Blueprint;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

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
    public function __construct(Blueprint $table, ?Closure $callback = null)
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
        return $this->table->addColumn($column, Types::GUID);
    }

    /**
     * Specify the primary key(s) for the table.
     *
     * @param string|string[] $columns
     * @param string|false $indexName
     *
     * @return Blueprint|null
     */
    public function primary($columns, $indexName = false): ?Blueprint
    {
        $columns = is_array($columns) ? $columns : [$columns];

        return $this->table->setPrimaryKey($columns, $indexName);
    }

    /**
     * Specify a unique index for the table.
     *
     * @param string|string[] $columns
     * @param string       $name
     * @param mixed[]      $options
     *
     * @return Blueprint|null
     */
    public function unique($columns, $name = null, $options = []): ?Blueprint
    {
        $columns = is_array($columns) ? $columns : [$columns];

        return $this->table->addUniqueIndex($columns, $name, $options);
    }

    /**
     * Specify an index for the table.
     *
     * @param string|string[] $columns
     * @param string       $name
     * @param string[]     $flags
     * @param mixed[]      $options
     *
     * @return Blueprint|null
     */
    public function index($columns, $name = null, $flags = [], $options = []): ?Blueprint
    {
        $columns = is_array($columns) ? $columns : [$columns];

        return $this->table->addIndex($columns, $name, $flags, $options);
    }

    /**
     * Specify a foreign key for the table.
     *
     * @param string $table
     * @param string[]|string $localColumnNames
     * @param string[]|string $foreignColumnNames
     * @param mixed[] $options
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
    ): ?Blueprint
    {
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
        return $this->table->addColumn($column, Types::STRING, compact('length'));
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
        return $this->table->addColumn($column, Types::TEXT);
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
        return $this->table->addColumn($column, Types::INTEGER, compact('autoIncrement', 'unsigned'));
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
        return $this->table->addColumn($column, Types::SMALLINT, compact('autoIncrement', 'unsigned'));
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
        return $this->table->addColumn($column, Types::BIGINT, compact('autoIncrement', 'unsigned'));
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
        return $this->table->addColumn($column, Types::FLOAT, compact('precision', 'scale'));
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
        return $this->table->addColumn($column, Types::DECIMAL, compact('precision', 'scale'));
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
        return $this->table->addColumn($column, Types::BOOLEAN);
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
        return $this->table->addColumn($column, Types::JSON);
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
        return $this->table->addColumn($column, Types::DATE_MUTABLE);
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
        return $this->table->addColumn($column, Types::DATETIME_MUTABLE);
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
        return $this->table->addColumn($column, Types::DATETIMETZ_MUTABLE);
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
        return $this->table->addColumn($column, Types::TIME_MUTABLE);
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
        return $this->table->addColumn($column, Types::DATETIME_MUTABLE);
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
        return $this->table->addColumn($column, Types::DATETIMETZ_MUTABLE);
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
        return $this->table->addColumn($column, Types::BINARY, compact('length'))->setNotnull(false);
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
     * @return Blueprint|null
     */
    public function dropColumn(string $column): ?Blueprint
    {
        return $this->table->dropColumn($column);
    }
}
