<?php

namespace Matze\Core\Assets\Rules;

class MergableFilesDefinition {

	/**
	 * @var string[]
	 */
	public $input_files;

	/**
	 * @var string
	 */
	public $output_file_name;

	/**
	 * @param string $output_file_name
	 * @param string[] $input_files
	 */
	public function __construct($output_file_name, array $input_files) {
		$this->input_files = $input_files;
		$this->output_file_name = $output_file_name;
	}

}