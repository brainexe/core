<?php

use BrainExe\Core\Core;

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT', realpath(__DIR__ . '/..') . '/');

return (new Core())->boot();
