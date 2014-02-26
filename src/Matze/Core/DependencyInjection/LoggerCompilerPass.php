<?php

namespace Matze\Core\DependencyInjection;

use Monolog\Logger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @CompilerPass
 */
class LoggerCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		if ($container->getParameter('debug')) {
			$logger = $container->getDefinition('monolog.Logger');
			$logger->addMethodCall('pushHandler', [new Definition('Monolog\Handler\ChromePHPHandler')]);
			$logger->addMethodCall('pushHandler', [new Definition('Monolog\Handler\StreamHandler', ['php://stdout', Logger::DEBUG])]);
		}
	}
}
