<?php

namespace Matze\Core\Assets\Rules;

abstract class MergableProcessor extends Processor {
	/**
	 * @var boolean
	 */
	protected $_debug;

	/**
	 * @var string
	 */
	protected $_yui_jar;

	/**
	 * @Inject({"%yui.jar%", "%debug%"})
	 * @param string $yui_jar
	 * @param boolean $debug
	 */
	public function setConfig($yui_jar, $debug) {
		$this->_yui_jar = $yui_jar;
		$this->_debug = $debug;
	}

	/**
	 * @var MergableFilesDefinition[]
	 */
	public $files;

	/**
	 * @var string
	 */
	public $fallback;

	/**
	 * @param MergableFilesDefinition $file_definition
	 */
	public function addDefinition(MergableFilesDefinition $file_definition) {
		$this->files[$file_definition->output_file_name] = $file_definition;
	}

	/**
	 * @param string $fallback
	 */
	public function setFallback($fallback) {
		$this->fallback = $fallback;
	}


} 