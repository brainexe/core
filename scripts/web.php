<?php

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use BrainExe\Core\Application\AppKernel;

/** @var Container $dic */
$dic = include __DIR__ . '/../src/bootstrap.php';

$request = Request::createFromGlobals();

/** @var AppKernel $kernel */
$kernel = $dic->get('AppKernel');
$response = $kernel->handle($request);

$response->prepare($request);
$response->send();
