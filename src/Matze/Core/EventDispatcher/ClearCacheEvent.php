<?php

namespace Matze\Core\EventDispatcher;

use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheEvent extends AbstractEvent {
	const NAME = 'cache.clear';

	/**
	 * @var OutputInterface
	 */
	public $output;

	public function __construct(OutputInterface $output) {
		$this->event_name = self::NAME;
		$this->output = $output;
	}
} 