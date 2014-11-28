<?php

namespace BrainExe\Core;

use BrainExe\Core\DependencyInjection\Rebuild;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
		$rebuild = new Rebuild();

		return $rebuild->rebuildDIC($boot);
	}
} 
