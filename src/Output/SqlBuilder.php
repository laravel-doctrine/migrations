<?php

namespace LaravelDoctrine\Migrations\Output;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use LaravelDoctrine\Migrations\Configuration\Configuration;

class SqlBuilder
{
	/**
	 * @param Configuration $configuration
	 * @param Schema $from
	 * @param Schema $to
	 *
	 * @return string
	 * @throws DBALException
	 */
    public function up(Configuration $configuration, Schema $from, Schema $to)
    {
        return $this->build(
            $configuration,
            $from->getMigrateToSql($to, $configuration->getConnection()->getDatabasePlatform())
        );
    }

	/**
	 * @param Configuration $configuration
	 * @param Schema $from
	 * @param Schema $to
	 *
	 * @return string
	 * @throws DBALException
	 */
    public function down(Configuration $configuration, Schema $from, Schema $to)
    {
        return $this->build(
            $configuration,
            $from->getMigrateFromSql($to, $configuration->getConnection()->getDatabasePlatform())
        );
    }

	/**
	 * @param Configuration $configuration
	 * @param array $queries
	 *
	 * @return string
	 * @throws DBALException
	 */
    public function build(Configuration $configuration, array $queries = [])
    {
        $platform = $configuration->getConnection()->getDatabasePlatform()->getName();

        $code = [];
        foreach ($queries as $query) {
            if (stripos($query, $configuration->getMigrationsTableName()) !== false) {
                continue;
            }
            $code[] = sprintf("\$this->addSql(%s);", var_export($query, true));
        }

        if (!empty($code)) {
            array_unshift(
                $code,
                sprintf(
                    "\$this->abortIf(\$this->connection->getDatabasePlatform()->getName() != %s, %s);",
                    var_export($platform, true),
                    var_export(sprintf("Migration can only be executed safely on '%s'.", $platform), true)
                ),
                ""
            );
        }

        return implode("\n", $code);
    }
}
