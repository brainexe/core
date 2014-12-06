<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class ListServicesCommand extends AbstractCommand {

	use EventDispatcherTrait;

	/**
	 * @var Rebuild
	 */
	private $rebuild;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('debug:list:services')->setDescription('List all services');
	}

	/**
	 * @inject("@Core.Rebuild")
	 * @param Rebuild $rebuild
	 */
	public function __construct(Rebuild $rebuild) {
		$this->rebuild = $rebuild;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$dic = $this->rebuild->rebuildDIC(false);

		$table = new Table($output);
		$table->setHeaders(['service-id', 'public']);

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
