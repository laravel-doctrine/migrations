<?php

namespace LaravelDoctrine\Migrations\Tests;

use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Illuminate\Console\Command;
use LaravelDoctrine\Migrations\Console\DiffCommand;
use LaravelDoctrine\Migrations\Console\DumpSchemaCommand;
use LaravelDoctrine\Migrations\Console\ExecuteCommand;
use LaravelDoctrine\Migrations\Console\GenerateCommand;
use LaravelDoctrine\Migrations\Console\LatestCommand;
use LaravelDoctrine\Migrations\Console\ListCommand;
use LaravelDoctrine\Migrations\Console\MigrateCommand;
use LaravelDoctrine\Migrations\Console\RollupCommand;
use LaravelDoctrine\Migrations\Console\StatusCommand;
use LaravelDoctrine\Migrations\Console\SyncMetadataCommand;
use LaravelDoctrine\Migrations\Console\VersionCommand;
use function array_keys;
use function implode;
use function in_array;
use function PHPUnit\Framework\assertSame;
use function print_r;
use function var_export;

class CommandConfigurationTest extends \PHPUnit\Framework\TestCase
{

    public function testAllCommandsAreConfiguredCorrectly(): void
    {
        $commands = [
            DiffCommand::class => \Doctrine\Migrations\Tools\Console\Command\DiffCommand::class,
            ExecuteCommand::class => \Doctrine\Migrations\Tools\Console\Command\ExecuteCommand::class,
            LatestCommand::class => \Doctrine\Migrations\Tools\Console\Command\LatestCommand::class,
            StatusCommand::class => \Doctrine\Migrations\Tools\Console\Command\StatusCommand::class,
            MigrateCommand::class => \Doctrine\Migrations\Tools\Console\Command\MigrateCommand::class,
            VersionCommand::class => \Doctrine\Migrations\Tools\Console\Command\VersionCommand::class,
            GenerateCommand::class => \Doctrine\Migrations\Tools\Console\Command\GenerateCommand::class,
            SyncMetadataCommand::class => \Doctrine\Migrations\Tools\Console\Command\SyncMetadataCommand::class,
            RollupCommand::class => \Doctrine\Migrations\Tools\Console\Command\RollupCommand::class,
            ListCommand::class => \Doctrine\Migrations\Tools\Console\Command\ListCommand::class,
            DumpSchemaCommand::class => \Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand::class,
        ];

        foreach ($commands as $ourCommandClass => $doctrineCommandClass) {
            /** @var Command $ourCommand */
            $ourCommand = new $ourCommandClass();
            /** @var DoctrineCommand $theirCommand */
            $theirCommand = new $doctrineCommandClass();

            self::assertDefinedSameOptions($ourCommand, $theirCommand);
            self::assertSameCommandConfiguration($ourCommand, $theirCommand);
        }
    }

    private static function assertSameCommandConfiguration(Command $ourCommand, DoctrineCommand $theirCommand): void
    {
        // We set a default value for these options
        $optionsIgnoredForRequired = ['em', 'filter-expression', 'filter-tables'];

        // Assert option configuration
        foreach ($ourCommand->getDefinition()->getOptions() as $ourOption) {
            foreach ($theirCommand->getDefinition()->getOptions() as $theirOption) {
                if ($ourOption->getName() === $theirOption->getName()) {
                    // Assert property is required in our and their configuration
                    if (in_array($ourOption->getName(), $optionsIgnoredForRequired) === false) {
                        // Laravel do not mark CLI options as required when a value is specified..
                        if (empty($ourOption->getDefault())) {
                            self::assertEquals($ourOption->isValueRequired(), $theirOption->isValueRequired(), "Mismatch 'required' state on option value for {$ourCommand->getName()} {$ourOption->getName()}. Their '{$theirOption->isValueRequired()}', our: '{$ourOption->isValueRequired()}'");
                        }
                    }
                    self::assertEquals($ourOption->acceptValue(), $theirOption->acceptValue(), "Mismatch 'acceptValue' state on option value for {$ourCommand->getName()} {$ourOption->getName()}. Their '{$theirOption->acceptValue()}', our: '{$ourOption->acceptValue()}'");
                    self::assertEquals($ourOption->isArray(), $theirOption->isArray(), "Mismatch array support for {$ourCommand->getName()} on argument {$ourOption->getName()}, should be " . var_export($theirOption->isArray(), true));
                    self::assertEquals($ourOption->isNegatable(), $theirOption->isNegatable(), "Mismatch 'negatable' for {$ourCommand->getName()} on argument {$ourOption->getName()}, should be " . var_export($theirOption->isNegatable(), true));

                    // Assert default values matches
                    $theirDefaultValue = var_export($theirOption->getDefault(), true);
                    $ourDefaultValue = var_export($ourOption->getDefault(), true);
                    self::assertSame($ourOption->getDefault(), $theirOption->getDefault(), "Mismatch default value for {$ourCommand->getName()} {$ourOption->getName()}. Their: '{$theirDefaultValue}', our: '{$ourDefaultValue}'");
                }
            }
        }

        // Assert argument configuration
        foreach ($ourCommand->getDefinition()->getArguments() as $ourArgument) {
            foreach ($theirCommand->getDefinition()->getArguments() as $theirArgument) {
                if ($ourArgument->getName() === $theirArgument->getName()) {
                    self::assertEquals($ourArgument->isArray(), $theirArgument->isArray(), "Mismatch array support for {$ourCommand->getName()} on argument {$ourArgument->getName()}, should be " . var_export($theirArgument->isArray(), true));
                    self::assertEquals($ourArgument->isRequired(), $theirArgument->isRequired(), "Mismatch required state for {$ourCommand->getName()} on argument {$ourArgument->getName()}, should be " . var_export($theirArgument->isRequired(), true));

                    $theirDefaultValue = var_export($theirArgument->getDefault(), true);
                    $ourDefaultValue = var_export($ourArgument->getDefault(), true);
                    self::assertEquals($ourArgument->getDefault(), $theirArgument->getDefault(), "Mismatch default value for {$ourCommand->getName()} {$ourArgument->getName()}. Their: '{$theirDefaultValue}', our: '{$ourDefaultValue}'");
                }
            }
        }
    }

    private static function assertDefinedSameOptions(Command $ourCommand, DoctrineCommand $theirCommand): void
    {
        // Options we do not support. They are defined on DoctrineCommand and applies for all their commands.
        $globalIgnoredOptions = [
            'configuration', 'conn', 'db-configuration', 'namespace'
        ];

        $commandName = $ourCommand->getName();

        $ourOptionNames = array_keys($ourCommand->getDefinition()->getOptions());
        $theirOptionNames = array_keys($theirCommand->getDefinition()->getOptions());

        $ourExtraOptions = [];
        $doctrineExtraOptions = [];

        foreach ($ourOptionNames as $ourOption) {
            if ($ourOption === 'connection') {
                // Our custom connection option
                continue;
            }

            if (in_array($ourOption, $theirOptionNames, true) === false) {
                $ourExtraOptions[] = $ourOption;
            }
        }


        foreach ($theirOptionNames as $theirOption) {
            if (in_array($theirOption, $globalIgnoredOptions, true)) {
                continue;
            }

            if (in_array($theirOption, $ourOptionNames, true) === false) {
                $doctrineExtraOptions[] = $theirOption;
            }
        }

        self::assertEmpty($ourExtraOptions, 'Command '.$commandName.' has options not supported by doctrine command: ' . implode(", ", $ourExtraOptions));
        self::assertEmpty($doctrineExtraOptions, 'Command '.$commandName.' is missing options from doctrine command: ' . implode(", ", $doctrineExtraOptions));

    }
}
