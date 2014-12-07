<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @codeCoverageIgnore
 */
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
		if (!$container->getParameter('core_standalone')) {
			return;
		}

		foreach ($container->getDefinitions() as $definition) {
			$definition->setPublic(true);
		}

		// @todo still needed here?
		$container->set(MessageQueueTestService::ID, new MessageQueueTestService());
	}
}
