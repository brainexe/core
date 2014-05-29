<?php

namespace Matze\Core\Twig\TransExtension;

use Twig_Compiler;
use Twig_Node_Expression;

class TransNode extends \Twig_Node {
	/**
	 * @var Twig_Node_Expression
	 */
	private $_string;

	/**
	 * @var array
	 */
	private $_parameters;

	public function __construct(Twig_Node_Expression $string, array $parameters, $lineno) {
		parent::__construct(array(), array('string' => $string), $lineno);

		$this->_string = $string;
		$this->_parameters = $parameters;
	}

	public function compile(Twig_Compiler $compiler) {
		$compiler->addDebugInfo($this);

		if (empty($this->_parameters)) {
			$compiler
				->write("echo gettext(")
				->subcompile($this->_string)
				->write(");");
		} else {
			$compiler
				->write("echo sprintf(gettext(")
				->subcompile($this->_string)
				->write(")");

			foreach ($this->_parameters as $parameter) {
				$compiler
					->write(", ")
					->subcompile($parameter);
			}

			$compiler->write(")")->raw(";\n");
		}
	}
}