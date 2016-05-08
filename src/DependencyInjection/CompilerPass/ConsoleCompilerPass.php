<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass;
use BrainExe\Core\Console\ProxyCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class ConsoleCompilerPass implements CompilerPassInterface
{

    const TAG = 'console';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $console = $container->getDefinition('Console');
        $console->addMethodCall('setAutoExit', [false]);

        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        $commands = [];
        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var Command $command */
            $command = $container->get($serviceId);

            $proxyService = new Definition(ProxyCommand::class, [
                new Reference('service_container'),
                new Reference('Console'),
                $serviceId,
                $command->getName(),
                $command->getDescription(),
                $command->getAliases(),
                $this->formatDefinition($command->getDefinition())
            ]);
            $commands[] = $proxyService;
        }

        $console->addMethodCall('addCommands', [$commands]);
    }

    /**
     * @param InputDefinition $definition
     * @return array
     */
    private function formatDefinition(InputDefinition $definition)
    {
        $arguments = $this->getArguments($definition);

        foreach ($definition->getOptions() as $option) {
            $mode = $this->getOptionMode($option);

            $arguments[] = new Definition(
                InputOption::class,
                [
                    $option->getName(),
                    $option->getShortcut(),
                    $mode,
                    $option->getDescription(),
                    $option->getDefault() ?: null
                ]
            );
        }

        return $arguments;
    }

    /**
     * @param InputArgument $argument
     * @return int
     */
    private function getArgumentMode(InputArgument $argument)
    {
        $mode = 0;
        if ($argument->isRequired()) {
            $mode |= InputArgument::REQUIRED;
        }
        if ($argument->isArray()) {
            $mode |= InputArgument::IS_ARRAY;
        }
        if (!$argument->isRequired()) {
            $mode |= InputArgument::OPTIONAL;
        }

        return $mode;
    }

    /**
     * @param InputOption $option
     * @return int
     */
    private function getOptionMode(InputOption $option)
    {
        $mode = 0;
        if ($option->isArray()) {
            $mode |= InputOption::VALUE_IS_ARRAY;
        }
        if ($option->isValueOptional()) {
            $mode |= InputOption::VALUE_OPTIONAL;
        }
        if ($option->isValueRequired()) {
            $mode |= InputOption::VALUE_REQUIRED;
        }
        if (!$mode) {
            $mode = InputOption::VALUE_NONE;
        }

        return $mode;
    }

    /**
     * @param InputDefinition $definition
     * @return array
     */
    private function getArguments(InputDefinition $definition) : array
    {
        $arguments = [];

        foreach ($definition->getArguments() as $argument) {
            $mode = $this->getArgumentMode($argument);

            $arguments[] = new Definition(InputArgument::class, [
                $argument->getName(),
                $mode,
                $argument->getDescription(),
                $argument->getDefault()
            ]);
        }

        return $arguments;
    }
}
