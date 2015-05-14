<?php

namespace BrainExe\Core\Console;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Command;
use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Command
 */
class ListServicesCommand extends AbstractCommand
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
    protected function doExecute(InputInterface $input, OutputInterface $output)
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

        if ($visibility === 'public' && !$isPublic) {
            return;
        } elseif ($visibility === 'private' && $isPublic) {
            return;
        }

        $table->addRow([
           $id,
           $isPublic ? '<info>public</info>' : '<error>private</error>'
        ]);
    }
}
