<?php

namespace BrainExe\Core\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

class ProxyCommand extends Command
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @param Container $container
     * @param Application $application
     * @param string $serviceId
     * @param string $name
     * @param string $description
     * @param string|array $alias
     * @param array $definition
     */
    public function __construct(
        Container $container,
        Application $application,
        string $serviceId,
        string $name,
        $description,
        $alias,
        array $definition
    ) {

        $this->setName($name);
        $this->setDescription($description);
        $this->setAliases($alias);

        $this->container = $container;
        $this->serviceId = $serviceId;

        parent::__construct();

        $this->setDefinition($definition);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Command $child */
        $child = $this->container->get($this->serviceId);
        $child->setApplication($this->getApplication());
        $child->setDefinition($this->getDefinition());

        return $child->execute($input, $output);
    }
}
