<?php

use BrainExe\Core\Core;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . "/..") . '/');

$core = new Core();
$core->boot();
