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
	private $_self_update;
	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcher
	 */
	private $_event_dispatcher;

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

		$this->_self_update = $self_update;
		$this->_event_dispatcher = $event_dispatcher;
	}

	/**
	 * @{inheritdoc}
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return mixed|void
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$this->_event_dispatcher->addListener(SelfUpdateEvent::PROCESS, function(SelfUpdateEvent $event) use ($output) {
			$output->write($event->payload);
		});

		$this->_event_dispatcher->addListener(SelfUpdateEvent::ERROR, function(SelfUpdateEvent $event) use ($output) {
			$output->writeln(sprintf('<error>Error during update: %s</error>', $event->payload));
		});

		$this->_self_update->startUpdate();
	}
}