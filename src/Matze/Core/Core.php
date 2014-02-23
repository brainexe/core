<?php

namespace Matze\Core;

use Matze\Annotations\Loader\AnnotationLoader;
use Matze\Core\DependencyInjection\GlobalCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

define('CORE_ROOT', __DIR__);

if (!defined('ROOT')) {
	define('ROOT', CORE_ROOT . '/../../../');
}

define('MATZE_VENDOR_ROOT', ROOT . '/vendor/matze/');

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

		/** @var Container $dic */
		if (file_exists('cache/dic.php')) {
			include 'cache/dic.php';
			$dic = self::$service_container = new \DIC();
		} else {
			$dic = self::$service_container = self::rebuildDIC();
		}

		date_default_timezone_set($dic->getParameter('timezone'));

		// TODO fix error handler
//		/** @var Logger $logger */
//		$logger = self::$service_container->get('Monolog.Logger');
//		$error_handler = new ErrorHandler($logger);
//		$error_handler->registerErrorHandler();
//		$error_handler->registerExceptionHandler();
//		$error_handler->registerFatalHandler();

		return $dic;
	}

	/**
	 * @return ContainerBuilder
	 */
	public static function rebuildDIC() {
		$container_builder = new ContainerBuilder();
		$container_builder->setParameter('application.root', ROOT);

		$annotation_loader = new AnnotationLoader($container_builder);
		$annotation_loader->load('src/');
		$annotation_loader->load(CORE_ROOT . '/../../');

		$loader = new XmlFileLoader($container_builder, new FileLocator('config'));
		$loader->load(ROOT . '/app/container.xml');
		if (file_exists(ROOT . '/app/config.xml')) {
			$loader->load(ROOT . '/app/config.xml');
		}

		// load container.xml file from all "matze" components
		$config_finder = new Finder();
		$config_finder
			->in(MATZE_VENDOR_ROOT)
			->name('container.xml');

		foreach ($config_finder as $file) {
			/** @var SplFileInfo $file */
			$loader->load($file->getPathname());
		}

		$container_builder->addCompilerPass(new GlobalCompilerPass());
		$container_builder->compile();

		$dumper = new PhpDumper($container_builder);
		$container_content = $dumper->dump(['class' => 'DIC']);
		file_put_contents('cache/dic.php', $container_content);

		return $container_builder;
	}
} 