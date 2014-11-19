<?php

use BrainExe\Core\Core;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__."/..").'/');
define('CORE_ROOT', realpath(__DIR__."/..").'/');
define('CORE_STANDALONE', true);

global $dic;
$dic = Core::boot();