<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Core;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\GraphvizDumper;

/**
 * @Command
 */
class ListServicesCommand extends AbstractCommand {

	use EventDispatcherTrait;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('debug:list:services')
			->setDescription('List all services');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$dic = Core::rebuildDIC(false);

		$table = new Table($output);
		$table
			->setHeaders(['service-id', 'public']);


		$ids = $dic->getServiceIds();
		sort($ids);

		foreach ($ids as $id) {
			if (!$dic->hasDefinition($id)) {
				continue;
			}
			$definition = $dic->getDefinition($id);

			$table->addRow([
							   $id,
							   $definition->isPublic() ? '1' : '0'
						   ]);
		}


		$table->render();
	}

} 