<?php

use Matze\Core\Application\AppKernel;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

/** @var AppKernel $kernel */
$kernel = $dic->get('AppKernel');
$kernel->handle($request);
