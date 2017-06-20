<?php

use BrainExe\Core\Application\AppKernel;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

/** @var Container $dic */
$dic = include __DIR__ . '/../src/bootstrap.php';

$request = Request::createFromGlobals();

/** @var AppKernel $kernel */
$kernel   = $dic->get(AppKernel::class);
$kernel->handle($request);
