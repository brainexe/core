<?php

namespace BrainExe\Core;

use BrainExe\Annotations\Loader\AnnotationLoader;
use BrainExe\Core\DependencyInjection\CompilerPass\GlobalCompilerPass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

if (!defined('CORE_ROOT')) {
	define('CORE_ROOT', __DIR__);
}

if (!defined('ROOT')) {
	define('ROOT', realpath(CORE_ROOT . '/../').'/');
}

if (!defined('BRAINEXE_VENDOR_ROOT')) {
	define('BRAINEXE_VENDOR_ROOT', ROOT . 'vendor/brainexe/');
}

if (!defined('CORE_STANDALONE')) {
	define('CORE_STANDALONE', false);
}

/**
 * @todo non-static class for rebuild dic
 */
class Core {

	/**
	 * @return Container
	 */
	public static function boot() {
		chdir(ROOT);
		umask(0);

		$files = glob('cache/dic_*.php');

		/** @var Container $dic */
		if ($files) {
			include_once $files[0];
			preg_match('/dic_([\d]*)/', $files[0], $matches);
			$class = $matches[0];
			$dic   = new $class();
		} else {
			$dic = self::rebuildDIC();
		}

		date_default_timezone_set($dic->getParameter('timezone'));

		// TODO improve
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
	 * @param boolean $boot
	 * @return ContainerBuilder
	 */
	public static function rebuildDIC($boot = true) {
		$container_builder = new ContainerBuilder();

		$annotation_loader = new AnnotationLoader($container_builder);
		$annotation_loader->load('src/');
		if (!CORE_STANDALONE) {
			$annotation_loader->load(CORE_ROOT);

			$app_finder = new Finder();
			$app_finder->directories()
				->in(BRAINEXE_VENDOR_ROOT)
				->depth(1)
				->name('src');

			foreach ($app_finder as $dir) {
				/** @var SplFileInfo $dir */
				$annotation_loader->load($dir->getPathname());
			}
		}

		$container_builder->addCompilerPass(new GlobalCompilerPass());
		$container_builder->compile();

		$random_id      = mt_rand();
		$container_name = sprintf('dic_%d', $random_id);
		$container_file = sprintf('cache/dic_%d.php', $random_id);

		foreach (glob('cache/dic_*.php') as $file) {
			unlink($file);
		}

		$dumper            = new PhpDumper($container_builder);
		$container_content = $dumper->dump(['class' => $container_name]);
		file_put_contents($container_file, $container_content);
		chmod($container_file, 0777);

		$dumper            = new XmlDumper($container_builder);
		$container_content = $dumper->dump();
		file_put_contents('cache/dic.xml', $container_content);

		if ($boot) {
			return self::boot();
		}

		return $container_builder;
	}
} 
