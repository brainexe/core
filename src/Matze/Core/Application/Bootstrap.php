<?php

namespace Matze\Core\Application;

use DirectoryIterator;
use SplFileInfo;
use Symfony\Component\Yaml\Parser;

class Bootstrap {

	/**
	 * @var array
	 */
	public $routes = array();

	public function __construct() {
		$this->loadConfiguration();
	}

	private function loadConfiguration() {
		$yaml = new Parser();

		$fileIterator = new DirectoryIterator(__DIR__ . '/config/routes');

		foreach ($fileIterator as $file) {
			/** @var SplFileInfo $file */
			if ($file->isFile()) {
				$routes = $yaml->parse(file_get_contents(__DIR__ . '/config/routes/' . $file->getFilename()));
				$this->routes = array_merge($this->routes, $routes);
			}
		}
	}
}