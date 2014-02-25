<?php

namespace Matze\Core;

use Matze\Annotations\Loader\AnnotationLoader;
use Matze\Core\DependencyInjection\GlobalCompilerPass;
use Monolog\ErrorHandler;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

define('CORE_ROOT', __DIR__);

if (!defined('ROOT')) {
	define('ROOT', CORE_ROOT . '/../../../');
}

define('MATZE_VENDOR_ROOT', ROOT . '/vendor/matze/');

class Core {

	/**
	 * @return Container
	 */
	public static function boot() {
		chdir(ROOT);

		/** @var Container $dic */
		if (file_exists('cache/dic.php')) {
			include 'cache/dic.php';
			$dic = new \DIC();
		} else {
			$dic = self::rebuildDIC();
		}

		date_default_timezone_set($dic->getParameter('timezone'));

		$dic->get('monolog.ErrorHandler');

		return $dic;
	}

	/**
	 * @return ContainerBuilder
	 */
	public static function rebuildDIC() {
		$container_builder = new ContainerBuilder();

		$annotation_loader = new AnnotationLoader($container_builder);
		$annotation_loader->load('src/');
		$annotation_loader->load(CORE_ROOT . '/../../');

		$container_builder->addCompilerPass(new GlobalCompilerPass());
		$container_builder->compile();

		$dumper = new PhpDumper($container_builder);
		$container_content = $dumper->dump(['class' => 'DIC']);
		file_put_contents('cache/dic.php', $container_content);

		return $container_builder;
	}
} 