<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Schema;

use Closure;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Table as Blueprint;
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
     * @param ?string $indexName
     *
     * @return Blueprint|null
     */
    public function primary($columns, ?string $indexName = null, bool $isClustered = false): ?Blueprint
    {
        $columns = is_array($columns) ? $columns : [$columns];

        $constraint = PrimaryKeyConstraint::editor()
            ->setName($this->makeName($indexName))
            ->setColumnNames(...$this->convertColumns($columns))
            ->setIsClustered($isClustered)
            ->create();
        return $this->table->addPrimaryKeyConstraint($constraint);
    }

    private function isQuoted(?string $name): bool
    {
        return $name !== null && 
            str_starts_with($name, '"') &&
            str_ends_with($name, '"');
    }

    /**
     * Creates an unqualified name.
     *
     * @param ?string $name
     * @return ?UnqualifiedName
     */
    private function makeName(?string $name): ?UnqualifiedName
    {
        return $this->isQuoted($name) ? $this->makeQuotedName($name) : $this->makeUnquotedName($name);
    }

    /**
     * Creates an unqualified quoted name.
     *
     * @param ?string $name
     * @return ?UnqualifiedName
     */
    private function makeQuotedName(?string $name): ?UnqualifiedName
    {
        // UnqalifiedName identifier needs to be non-empty string
        if ($name === null) return null;
        if (!$this->isQuoted($name)) return null;

        $inner = substr($name, 1, -1);
        if ($inner === '') return null;

        /** @var non-empty-string $name */
        return UnqualifiedName::quoted($name);
    }

    /**
     * Creates an unqualified unquoted name.
     *
     * @param ?string $name
     * @return ?UnqualifiedName
     */
    private function makeUnquotedName(?string $name): ?UnqualifiedName
    {
        if ($name === null) return null;
        return strlen($name) === 0 ? null : UnqualifiedName::unquoted($name);
    }

    /**
     * Converts string[] of columns to UnqualifiedName[] of columns
     *
     * @param string[] $columns
     * @return UnqualifiedName[]
     */
    private function convertColumns(array $columns): array
    {
        return array_map(
            fn($col) => $this->makeName($col),
            $columns
        );
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

        if (count($columns) === 0) {
            throw new \InvalidArgumentException('You must specify at least one column for a unique index.');
        }

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

        if (count($columns) === 0) {
            throw new \InvalidArgumentException('You must specify at least one column for an index.');
        }

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
        $local = is_array($localColumnNames) ? $localColumnNames : [$localColumnNames];
        $foreign = is_array($foreignColumnNames) ? $foreignColumnNames : [$foreignColumnNames];

        // Enforce non-empty arrays
        if ($local === [] || $foreign === []) {
            throw new \InvalidArgumentException("Foreign key must reference at least one local and one foreign column.");
        }

        // Re-index to ensure list shape (not associative)
        /** @var non-empty-list<string> $local */
        $local = array_values($local);

        /** @var non-empty-list<string> $foreign */
        $foreign = array_values($foreign);

        return $this->table->addForeignKeyConstraint($table, $local, $foreign, $options, $constraintName);
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
