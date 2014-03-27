<?php

namespace Matze\Core;

use Matze\Annotations\Loader\AnnotationLoader;
use Matze\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

define('CORE_ROOT', __DIR__);

if (!defined('ROOT')) {
	define('ROOT', realpath(CORE_ROOT . '/../../../').'/');
}

if (!defined('MATZE_VENDOR_ROOT')) {
	define('MATZE_VENDOR_ROOT', ROOT . '/vendor/matze/');
}

class Core {

	/**
	 * @return Container
	 */
	public static function boot() {
		chdir(ROOT);
		umask(0);

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
	 * @param string $locale
	 */
	public static function setLocale($locale) {
		putenv("LANG=$locale.UTF-8");
		setlocale(LC_MESSAGES, "$locale.UTF-8");

		$domain = 'messages';
		bindtextdomain($domain, ROOT . "/lang/");
		bind_textdomain_codeset($domain, 'UTF-8');
		textdomain($domain);
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

		if (!defined('PHPUNIT')) {
			$container_file = 'cache/dic.php';
			$dumper = new PhpDumper($container_builder);
			$container_content = $dumper->dump(['class' => 'DIC']);
			file_put_contents($container_file, $container_content);
			chmod($container_file, 0777);
		}

		return $container_builder;
	}
} 
