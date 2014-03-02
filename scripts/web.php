<?php

use Matze\Core\Application\AppKernel;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**	@var Container $dic */
/** @var Session $session */
$session = $dic->get('RedisSession');

$request = Request::createFromGlobals();
$request->setSession($session);

/** @var AppKernel $kernel */
$kernel = $dic->get('AppKernel');
$kernel->handle($request);
