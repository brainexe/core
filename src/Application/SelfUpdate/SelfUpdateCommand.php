<?php

namespace BrainExe\Core\Application\SelfUpdate;

use BrainExe\Core\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @Command
 */
class SelfUpdateCommand extends AbstractCommand {

	/**
	 * @var SelfUpdate
	 */
	private $_selfUpdate;

	/**
	 * @var EventDispatcher
	 */
	private $_eventDispatcher;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('self_update')
			->setDescription('Start Self Update');
	}

	/**
	 * @Inject({"@SelfUpdate", "@EventDispatcher"})
	 * @param SelfUpdate $self_update
	 * @param EventDispatcher $event_dispatcher
	 */
	public function __construct(SelfUpdate $self_update, EventDispatcher $event_dispatcher) {
		parent::__construct();

		$this->_selfUpdate = $self_update;
		$this->_eventDispatcher = $event_dispatcher;
	}

	/**
	 * @{inheritdoc}
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return mixed|void
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$this->_eventDispatcher->addListener(SelfUpdateEvent::PROCESS, function(SelfUpdateEvent $event) use ($output) {
			$output->write($event->payload);
		});

		$this->_eventDispatcher->addListener(SelfUpdateEvent::ERROR, function(SelfUpdateEvent $event) use ($output) {
			$output->writeln(sprintf('<error>Error during update: %s</error>', $event->payload));
		});

		$this->_selfUpdate->startUpdate();
	}
}
