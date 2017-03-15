<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Annotations\Command as CommandAnnotation;
use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CommandAnnotation
 */
class ListServicesCommand extends Command
{

    const TYPE_PRIVATE   = 'private';
    const TYPE_PROTECTED = 'protected';
    const TYPE_PUBLIC    = 'public';

    use EventDispatcherTrait;

    /**
     * @var Rebuild
     */
    private $rebuild;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('debug:list:services')
            ->setDescription('List all services')
            ->addArgument('visibility', InputArgument::OPTIONAL, 'public, protected or private');
    }

    /**
     * @param Rebuild $rebuild
     */
    public function __construct(Rebuild $rebuild)
    {
        $this->rebuild = $rebuild;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->rebuild->buildContainer();

        $table = new Table($output);
        $table->setHeaders(['service-id', 'tags', 'visibility']);

        $ids = $this->container->getServiceIds();

        sort($ids);

        $visibility = $input->getArgument('visibility');

        foreach ($ids as $id) {
            if ($this->container->hasDefinition($id)) {
                $this->addDefinition($id, $table, $visibility);
            }
        }

        $table->render();
    }

    /**
     * @param string $id
     * @param Table $table
     * @param bool $restrictedVisibility
     */
    private function addDefinition($id, Table $table, $restrictedVisibility)
    {
        $definition = $this->container->getDefinition($id);

        if (!$definition->isPublic()) {
            $currentVisibility = self::TYPE_PRIVATE;
            $color = 'error';
        } elseif (strpos($id, '__') === 0) {
            $currentVisibility = self::TYPE_PROTECTED;
            $color = 'comment';
        } else {
            $currentVisibility = self::TYPE_PUBLIC;
            $color = 'info';
        }

        if (!$restrictedVisibility || $restrictedVisibility === $currentVisibility) {
            $table->addRow([
                $id,
                implode(', ', array_keys($definition->getTags())),
                "<$color>$currentVisibility</$color>"
            ]);
        }
    }
}
