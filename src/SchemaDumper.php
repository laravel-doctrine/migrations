<?php

namespace LaravelDoctrine\Migrations;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Table;
use Doctrine\Migrations\Exception\NoTablesFound;
use Doctrine\Migrations\Generator\SqlGenerator;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Output\MigrationFileGenerator;

final class SchemaDumper
{
    /**
     * @var MigrationFileGenerator
     */
    private $migrationFileGenerator;

    public function __construct(MigrationFileGenerator $migrationFileGenerator)
    {
        $this->migrationFileGenerator = $migrationFileGenerator;
    }

    public function dump(Configuration $configuration, bool $formatted = false, int $lineLength = 120): string
    {
        // The platform check and a skip if statement will be added by the SchemaDumper
        $configuration->setCheckDatabasePlatform(false);

        $platform = $configuration->getConnection()->getDatabasePlatform();
        $migrationSqlGenerator = new SqlGenerator($configuration, $platform);

        $up   = [];
        $down = [];
        foreach ($configuration->getConnection()->getSchemaManager()->createSchema()->getTables() as $table) {
            $up = $this->addUpCodeForTable($up, $platform, $migrationSqlGenerator, $table, $formatted, $lineLength);
            $down = $this->addDownCodeForTable($down, $platform, $migrationSqlGenerator, $table, $formatted, $lineLength);
        }

        if (count($up) === 0) {
            throw NoTablesFound::new();
        }

        return $this->migrationFileGenerator->generate(
            $configuration,
            false,
            false,
            \implode("\n", $this->addPlatformCheck($up, $platform)),
            \implode("\n", $this->addPlatformCheck($down, $platform))
        );
    }

    private function addUpCodeForTable(
        array $up,
        AbstractPlatform $platform,
        SqlGenerator $migrationSqlGenerator,
        Table $table,
        bool $formatted,
        int $lineLength
    ): array {
        $upSql = $platform->getCreateTableSQL($table);

        $upCode = $migrationSqlGenerator->generate(
            $upSql,
            $formatted,
            $lineLength
        );
        if ($upCode !== '') {
            $up[] = $upCode;
        }

        return $up;
    }

    private function addDownCodeForTable(
        array $down,
        AbstractPlatform $platform,
        SqlGenerator $migrationSqlGenerator,
        Table $table,
        bool $formatted,
        int $lineLength
    ): array {
        $downSql = [$platform->getDropTableSQL($table)];

        $downCode = $migrationSqlGenerator->generate(
            $downSql,
            $formatted,
            $lineLength
        );
        if ($downCode !== '') {
            $down[] = $downCode;
        }

        return $down;
    }

    private function addPlatformCheck(array $statements, AbstractPlatform $platform): array
    {
        \array_unshift(
            $statements,
            \sprintf(
                '$this->skipIf($this->connection->getDatabasePlatform()->getName() !== %s, %s);',
                \var_export($platform->getName(), true),
                \var_export(
                    \sprintf("Migration can only be executed safely on '%s'.", $platform->getName()),
                    true
                )
            ),
        );

        return $statements;
    }
}
