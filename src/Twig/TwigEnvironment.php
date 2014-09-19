<?php

namespace Matze\Core\Twig;

use Twig_Environment;
use Twig_NodeInterface;

class TwigEnvironment extends Twig_Environment {

	/**
	 * Trim all whitespaces in compiled code
	 * {@inheritdoc}
	 */
	public function compile(Twig_NodeInterface $node) {
		$content = parent::compile($node);

		$content = str_replace("\\t", '', $content);
		$content = preg_replace('/>\s+</m', '><', $content);
		$content = preg_replace("/<!--.*?-->/", '', $content);
		$content = preg_replace("/ +}}/", '}}', $content);
		$content = preg_replace("/{{ +/", '{{', $content);

		return $content;
	}
} 