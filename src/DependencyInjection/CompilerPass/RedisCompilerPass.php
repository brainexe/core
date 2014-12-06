<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class RedisCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$redis = $container->getDefinition('redis');

		if ($redis_password = $container->getParameter('redis.password')) {
			$redis->addMethodCall('auth', [$redis_password]);
		}

		if ($redis_database = $container->getParameter('redis.database')) {
			$redis->addMethodCall('select', [$redis_database]);
		}
	}
}
