<?php

namespace Matze\Core\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

class MessageQueueEvent extends Event {

	const NAME = 'message_queue';

	/**
	 * @var string
	 */
	public $service_id;

	/**
	 * @var string
	 */
	public $method;

	/**
	 * @var array
	 */
	public $arguments;

	/**
	 * @return array
	 */
	public function toArray() {
		return [
			'service_id' => $this->service_id,
			'method' => $this->method,
			'arguments' => json_encode($this->arguments),
		];
	}

	/**
	 * @param string $service_id
	 * @param string $method
	 * @param array $arguments
	 */
	function __construct($service_id, $method, $arguments) {
		$this->method = $method;
		$this->arguments = $arguments;
		$this->service_id = $service_id;
	}

}