<?php

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Matze\Core\Application\AppKernel;

/** @var Container $dic */
$dic = include __DIR__ . '/../src/bootstrap.php';

$request = Request::createFromGlobals();

/** @var AppKernel $kernel */
$kernel = $dic->get('AppKernel');
$kernel->handle($request);
