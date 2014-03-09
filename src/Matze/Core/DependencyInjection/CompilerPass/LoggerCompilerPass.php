<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

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
		$logger = $container->getDefinition('monolog.Logger');

		if (defined('PHPUNIT')) {
			$logger->removeMethodCall('pushHandler');
			$logger->removeMethodCall('pushHandler');
			$logger->addMethodCall('pushHandler', [new Definition('Monolog\Handler\TestHandler')]);

		} elseif ($container->getParameter('debug')) {
			$logger->addMethodCall('pushHandler', [new Definition('Monolog\Handler\ChromePHPHandler')]);
			$logger->addMethodCall('pushHandler', [new Definition('Monolog\Handler\StreamHandler', ['php://stdout', Logger::INFO])]);
		}

	}
}
