<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Core;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\GraphvizDumper;

/**
 * @Command
 */
class GraphvizDumpCommand extends AbstractCommand {

	use EventDispatcherTrait;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('graphviz:dump')
			->setDescription('Dump container to graphviz');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$dic = Core::rebuildDIC();

		$dumper = new GraphvizDumper($dic);
		$content = $dumper->dump();
		file_put_contents('cache/dic.gv', $content);
		exec('dot -Tpng cache/dic.gv -o graph.png; rm cache/dic.gv');
	}

} 