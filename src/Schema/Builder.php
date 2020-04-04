<?php

namespace LaravelDoctrine\Migrations\Schema;

use Closure;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;

class Builder
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Modify a table on the schema.
     *
     * @param string  $table
     * @param Closure $callback
     *
     * @return \Doctrine\DBAL\Schema\Table|string
     */
    public function create($table, Closure $callback)
    {
        $table = $this->schema->createTable($table);

        $callback($this->build($table));

        return $table;
    }

    /**
     * Create a new table on the schema.
     *
     * @param string   $table
     * @param \Closure $callback
     *
     * @throws SchemaException
     * @return \Doctrine\DBAL\Schema\Table|string
     */
    public function table($table, Closure $callback)
    {
        $table = $this->schema->getTable($table);

        $callback($this->build($table));

        return $table;
    }

    /**
     * Drop a table from the schema.
     *
     * @param string $table
     *
     * @return Schema
     */
    public function drop($table)
    {
        return $this->schema->dropTable($table);
    }

    /**
     * Drop a table from the schema if it exists.
     *
     * @param string $table
     *
     * @return Schema
     */
    public function dropIfExists($table)
    {
        if ($this->schema->hasTable($table)) {
            return $this->drop($table);
        }
    }

    /**
     * Rename a table on the schema.
     *
     * @param string $from
     * @param string $to
     *
     * @return Schema
     */
    public function rename($from, $to)
    {
        return $this->schema->renameTable($from, $to);
    }

    /**
     * Determine if the given table exists.
     *
     * @param string $table
     *
     * @return bool
     */
    public function hasTable($table)
    {
        return $this->schema->hasTable($table);
    }

	/**
	 * Determine if the given table has a given column.
	 *
	 * @param string $table
	 * @param string $column
	 *
	 * @return bool
	 * @throws SchemaException
	 */
    public function hasColumn($table, $column)
    {
        return $this->schema->getTable($table)->hasColumn($column);
    }

	/**
	 * Determine if the given table has given columns.
	 *
	 * @param string $table
	 * @param array $columns
	 *
	 * @return bool
	 * @throws SchemaException
	 */
    public function hasColumns($table, array $columns)
    {
        $tableColumns = array_map('strtolower', array_keys($this->getColumnListing($table)));

        foreach ($columns as $column) {
            if (!in_array(strtolower($column), $tableColumns)) {
                return false;
            }
        }

        return true;
    }

	/**
	 * Get the column listing for a given table.
	 *
	 * @param string $table
	 *
	 * @return array
	 * @throws SchemaException
	 */
    public function getColumnListing($table)
    {
        return $this->schema->getTable($table)->getColumns();
    }

    /**
     * @param              $table
     * @param Closure|null $callback
     *
     * @return Table
     */
    protected function build($table, Closure $callback = null)
    {
        return new Table($table, $callback);
    }
}
