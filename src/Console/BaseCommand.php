<?php
declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Illuminate\Console\Command;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;

abstract class BaseCommand extends Command
{
    /**
     * @param DoctrineCommand $command
     * @return ArrayInput
     */
    protected function getDoctrineInput(DoctrineCommand $command): ArrayInput
    {
        $definition = $this->getDefinition();
        $inputArgs = [];

        foreach ($definition->getArguments() as $argument) {
            $argName = $argument->getName();

            if ($argName === 'command' || !$this->argumentExists($command, $argName)) {
                continue;
            }

            if ($this->hasArgument($argName)) {
                $inputArgs[$argName] = $this->argument($argName);
            }
        }

        foreach ($definition->getOptions() as $option) {
            $optionName = $option->getName();

            if ($optionName === 'em' || !$this->optionExists($command, $optionName)) {
                continue;
            }

            if ($this->input->hasOption($optionName)) {
                $inputArgs['--' . $optionName] = $this->input->getOption($optionName);
            }
        }

        $input = new ArrayInput($inputArgs);
        $input->setInteractive(!($this->input->getOption("no-interaction") ?? false));

        return $input;
    }

    private function argumentExists(\Symfony\Component\Console\Command\Command $command, string $argName): bool
    {
        foreach ($command->getDefinition()->getArguments() as $arg) {
            if ($arg->getName() === $argName) {
                return true;
            }
        }
        return false;
    }

    private function optionExists(\Symfony\Component\Console\Command\Command $command, string $optionName): bool
    {
        foreach ($command->getDefinition()->getOptions() as $option) {
            if ($option->getName() === $optionName) {
                return true;
            }
        }
        return false;
    }
}
