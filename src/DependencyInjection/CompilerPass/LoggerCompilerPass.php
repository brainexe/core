<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandlerTest;
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
			$logger->addMethodCall('pushHandler', [new Definition(TestHandlerTest::class)]);

		} elseif ($container->getParameter('debug')) {
			$logger->addMethodCall('pushHandler', [new Definition(ChromePHPHandler::class)]);
			$logger->addMethodCall('pushHandler', [new Definition(StreamHandler::class, ['php://stdout', Logger::INFO])]);
		}

	}
}
