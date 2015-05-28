<?php

namespace BrainExe\Core\Console;

use BrainExe\Annotations\Annotations\Inject;
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

    use EventDispatcherTrait;

    /**
     * @var Rebuild
     */
    private $rebuild;

    /**
     * @var ContainerBuilder
     */
    private $dic;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('debug:list:services')
            ->setDescription('List all services')
            ->addArgument('visibility', InputArgument::OPTIONAL, 'public or private');
    }

    /**
     * @Inject("@Core.Rebuild")
     * @param Rebuild $routes
     */
    public function __construct(Rebuild $routes)
    {
        $this->rebuild = $routes;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dic = $this->rebuild->rebuildDIC(false);

        $table = new Table($output);
        $table->setHeaders(['service-id', 'visibility']);

        $ids = $this->dic->getServiceIds();

        sort($ids);

        $visibility = $input->getArgument('visibility');

        foreach ($ids as $id) {
            if (!$this->dic->hasDefinition($id)) {
                continue;
            }
            $this->addDefinition($id, $table, $visibility);
        }

        $table->render();
    }

    /**
     * @param string $id
     * @param Table $table
     * @param bool $visibility
     */
    private function addDefinition($id, Table $table, $visibility)
    {
        $definition = $this->dic->getDefinition($id);

        $isPublic = $definition->isPublic();

        if ($this->isVisible($visibility, $isPublic)) {
            $table->addRow([
                $id,
                $isPublic ? '<info>public</info>' : '<error>private</error>'
            ]);
        }
    }

    /**
     * @param $visibility
     * @param $isPublic
     * @return bool
     */
    private function isVisible($visibility, $isPublic)
    {
        if ($visibility === 'public' && !$isPublic) {
            return false;
        } elseif ($visibility === 'private' && $isPublic) {
            return false;
        }

        return true;
    }

}
