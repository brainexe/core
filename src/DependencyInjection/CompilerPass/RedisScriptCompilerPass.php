<?php

namespace BrainExe\Core\DependencyInjection\CompilerPass;

use Exception;
use BrainExe\Core\Redis\RedisScriptInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class RedisScriptCompilerPass implements CompilerPassInterface {

	const TAG = 'redis_script';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$redis_script = $container->getDefinition('RedisScripts');

		$tagged_services = $container->findTaggedServiceIds(self::TAG);
		foreach (array_keys($tagged_services) as $service_id) {
			$definition = $container->getDefinition($service_id);
			/** @var RedisScriptInterface $class */
			$class = $definition->getClass();

			$reflection_class = new ReflectionClass($class);
			if (!$reflection_class->implementsInterface('BrainExe\Core\Redis\RedisScriptInterface')) {
				throw new Exception(sprintf("Class %s dies not implements the interface 'RedisScriptInterface'", $class));
			}
			$scripts = $class::getRedisScripts();

			foreach ($scripts as $name => $script) {
				$sha1 = sha1($script);
				$redis_script->addMethodCall('registerScript', [$name, $sha1, $script]);
			}
		}
	}
}
