<?php

use BrainExe\Core\Core;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . '/..').'/');
define('CORE_STANDALONE', true);

/** todo fix with #6 */
if (!function_exists('_')) {
    function _($string)
    {
        return $string;
    }
}
$core = new Core();
return $core->boot();
