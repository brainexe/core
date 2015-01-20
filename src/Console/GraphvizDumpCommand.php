<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\DependencyInjection\Rebuild;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\GraphvizDumper;

/**
 * @Command
 * @codeCoverageIgnore
 */
class GraphvizDumpCommand extends Command
{

    /**
     * @var Rebuild
     */
    private $rebuild;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('graphviz:dump')
            ->setDescription('Dump container to graphviz');
    }

    /**
     * @inject("@Core.Rebuild")
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
        $dic = $this->rebuild->rebuildDIC(false);

        $dumper  = new GraphvizDumper($dic);
        $content = $dumper->dump();

        file_put_contents('cache/dic.gv', $content);
        exec('dot -Tpng cache/dic.gv -o cache/graph.png; rm cache/dic.gv');

        $output->writeln('PNG: <info>cache/graph.png</info>');
        $output->writeln('GV:  <info>cache/dic.gv</info>');
    }
}
