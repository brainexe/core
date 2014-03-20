<?php

namespace Matze\Core\Twig;

use Twig_NodeInterface;

class TwigEnvironment extends \Twig_Environment {

	/**
	 * {@inheritdoc}
	 */
	public function compile(Twig_NodeInterface $node) {
		$content = parent::compile($node);

		$content = str_replace("\\t", '', $content);
		$content = preg_replace('/>\s+</m', '><', $content);

		return $content;
	}
} 