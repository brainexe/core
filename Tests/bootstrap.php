<?php

use BrainExe\Core\Core;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', basename(__DIR__ . '/../'));
define('PHPUNIT', true);
define('BRAINEXE_VENDOR_ROOT', basename(__DIR__.'/../../'));

global $dic;
$dic = Core::rebuildDIC();