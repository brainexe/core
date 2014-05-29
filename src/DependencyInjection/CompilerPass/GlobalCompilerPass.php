<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Monolog\Logger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GlobalCompilerPass implements CompilerPassInterface {

	const TAG = 'compiler_pass';

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$container->setParameter('application.root', ROOT);
		$container->setParameter('application.vendor_root', MATZE_VENDOR_ROOT);

		$service_ids = $container->findTaggedServiceIds(self::TAG);
		$service_priorities = [];

		foreach ($service_ids as $service_id => $tag) {
			$service_priorities[$service_id] = $tag[0]['priority'];
		}

		asort($service_priorities);
		$service_priorities = array_reverse($service_priorities);

		/** @var Logger $logger */
		$total_time = 0;
		$logger_store = [];
		foreach (array_keys($service_priorities) as $service_id) {
			$start_time = microtime(true);

			$container->get($service_id)->process($container);

			$total_time += $diff = microtime(true)-$start_time;

			$logger_store[] = sprintf('DIC: %0.2fms %s\n', $diff * 1000, $service_id);
		}

		/** @var Logger $logger */
		$logger = $container->get('monolog.logger');
		foreach ($logger_store as $log) {
			$logger->debug($log);
		}
		$logger->debug(sprintf('DIC: %0.2fms total time\n', $total_time * 1000));
	}
}