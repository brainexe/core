<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
		if (!defined('PHPUNIT')) {
			return;
		}

		foreach ($container->getDefinitions() as $definition) {
			$definition->setPublic(true);
		}

		$container->set(MessageQueueTestService::ID, new MessageQueueTestService());

	}
}
