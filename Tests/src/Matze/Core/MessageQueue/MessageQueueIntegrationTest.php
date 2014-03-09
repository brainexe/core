<?php

namespace Matze\Tests\Core\MessageQueue;

use Matze\Core\DependencyInjection\CompilerPass\MessageQueueTestService;
use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\MessageQueue\MessageQueueWorker;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MQTEstService {
	public function run() {

	}
}

class MessageQueueIntegrationTest extends \PHPUnit_Framework_TestCase{

	public function testThrowEvent() {
		/** @var $dic ContainerBuilder */
		global $dic;

		$event = new MessageQueueEvent(MessageQueueTestService::ID, 'run', [1, 2]);

		/** @var MessageQueueWorker $message_queue_worker */
		/** @var EventDispatcher $event_dispatcher */
		$message_queue_worker = $dic->get('MessageQueueWorker');
		$event_dispatcher = $dic->get('EventDispatcher');
		$event_dispatcher->dispatch(MessageQueueEvent::NAME, $event);

		$message_queue_worker->run(1);
	}
} 