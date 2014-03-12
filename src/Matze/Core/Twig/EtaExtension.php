<?php

namespace Matze\Core\Twig;

/**
 * @TwigExtension
 */
class EtaExtension extends \Twig_Extension {

	/**
	 * @var integer
	 */
	private $_now;

	public function __construct() {
		$this->_now = time();
	}

	public function getFilters() {
		return [
			new \Twig_SimpleFilter('eta', [$this, 'getEta', ['is_safe' => true]])
		];
	}

	public function getEta($timestamp){
		$difference = $this->_now - $timestamp;

		$periods = ["sec", "min", "hour", "day", "week", "month", "years", "decade"];
		$lengths = ["60","60","24","7","4.35","12","10"];

		if ($difference > 0) {
			$ending = "ago";
		} else {
			$difference = -$difference;
			$ending = "to go";
		}
		for($j = 0; $difference >= $lengths[$j]; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);
		if($difference != 1) $periods[$j].= "s";
		$text = "$difference $periods[$j] $ending";

		return $text;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'eta';
	}
}