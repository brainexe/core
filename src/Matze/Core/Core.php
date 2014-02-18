<?php

namespace Matze\Core;

define('CORE_ROOT', __DIR__);

if (defined('ROOT')) {
	define('ROOT', CORE_ROOT . '/../../../');
}

use Doctrine\Common\Annotations\AnnotationRegistry;
use Matze\Annotations\Loader\AnnotationLoader;
use Matze\Core\DependencyInjection\GlobalCompilerPass;
use Monolog\Logger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Core {

	/**
	 * @var Container
	 */
	private static $service_container;

	/**
	 * @return Container
	 */
	public static function getServiceContainer() {
		return self::$service_container;
	}

	/**
	 * @return Container
	 */
	public static function boot() {
		chdir(ROOT);

		date_default_timezone_set('Europe/Berlin');

		/** @var Container $dic */
		if (file_exists('cache/dic.php')) {
			include 'cache/dic.php';
			self::$service_container = new \DIC();
		} else {
			self::$service_container = self::rebuildDIC();
		}
		/** @var Logger $logger */
		$logger = self::$service_container->get('Monolog.Logger');

		// TODO fix error handler
//		$error_handler = new ErrorHandler($logger);
//		$error_handler->registerErrorHandler();
//		$error_handler->registerExceptionHandler();
//		$error_handler->registerFatalHandler();

		return self::$service_container;
	}

	/**
	 * @return ContainerBuilder
	 */
	public static function rebuildDIC() {
		$class_loader = include ROOT . '/vendor/autoload.php';

		AnnotationRegistry::registerLoader(function ($class) use ($class_loader) {
			return $class_loader->loadClass($class);
		});

		$container_builder = new ContainerBuilder();
		$annotation_loader = new AnnotationLoader($container_builder);
		$annotation_loader->load('src/');
		$annotation_loader->load(CORE_ROOT . '/../../');

		$loader = new XmlFileLoader($container_builder, new FileLocator('config'));
		$loader->load(ROOT . '/config/services.xml');
		$loader->load(ROOT . '/config/config.default.xml');
		$loader->load('services.xml');
		$loader->load('config.default.xml');
		if (file_exists('config/config.xml')) {
			$loader->load('config.xml');
		}

		$container_builder->addCompilerPass(new GlobalCompilerPass());
		$container_builder->compile();

		$dumper = new PhpDumper($container_builder);
		$container_content = $dumper->dump(['class' => 'DIC']);
		file_put_contents('cache/dic.php', $container_content);

		return $container_builder;
	}
} 