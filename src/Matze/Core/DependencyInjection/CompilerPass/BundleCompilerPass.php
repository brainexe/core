<?php

namespace Matze\Core\DependencyInjection\CompilerPass;

use Monolog\Logger;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPass
 */
class BundleCompilerPass implements CompilerPassInterface {

	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container) {
		$bundle = new MonologBundle();
		$bundle->build($container);
		$bundle->boot($container);
	}
}