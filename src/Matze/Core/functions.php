<?php

/**
 * @param string $text
 * @return string
 */
function t($text) {
	$args = func_get_args();
	unset($args[0]);
	$gettext = gettext($text);
	$return = @vsprintf($gettext, $args);
	if ($return === false) {
		trigger_error("Wrong parameter count in translation: $text => $gettext", E_USER_WARNING);
	}
	return $return;
}
