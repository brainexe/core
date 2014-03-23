<?php

namespace Matze\Core\Console;

use Matze\Core\Core;
use Matze\Core\EventDispatcher\ClearCacheEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\GraphvizDumper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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