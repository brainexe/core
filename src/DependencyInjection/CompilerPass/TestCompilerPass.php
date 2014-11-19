<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MessageQueueTestService {
	const ID = 'MessageQueueTestService';

	public function run() {

	}
}

/**
 * @CompilerPass(priority=1)
 */
class TestCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		if (!CORE_STANDALONE) {
			return;
		}

		foreach ($container->getDefinitions() as $definition) {
			$definition->setPublic(true);
		}

		$container->set(MessageQueueTestService::ID, new MessageQueueTestService());

	}
}
