<?php

use BrainExe\Core\Core;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . '/..') . '/');
define('CORE_STANDALONE', true);

$core = new Core();
return $core->boot();
