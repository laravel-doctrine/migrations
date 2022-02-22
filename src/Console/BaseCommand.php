<?php
declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\Command;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;

abstract class BaseCommand extends Command
{

    abstract public function handle(DependencyFactoryProvider $provider): int;

    /**
     * @param InputInterface $input
     * @param string[] $args
     * @return void
     */
    protected function getDoctrineInput(): ArrayInput {
        $definition = $this->getDefinition();
        $inputArgs = [];

        foreach ($definition->getArguments() as $argument) {
            $argName = $argument->getName();

            if ($argName === 'command') {
                continue;
            }

            if ($this->hasArgument($argName)) {
                $inputArgs[$argName] = $this->argument($argName);
            }
        }

        foreach ($definition->getOptions() as $option) {
            $optionName = $option->getName();

            if ($optionName === 'connection') {
                continue;
            }

            if ($this->input->hasOption($optionName)) {
                $inputArgs['--' . $optionName] = $this->input->getOption($optionName);
            }
        }
        return new ArrayInput($inputArgs);
    }
}