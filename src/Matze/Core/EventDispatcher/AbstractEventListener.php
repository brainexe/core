<?php

namespace Matze\Core\EventDispatcher;

use Matze\Core\Traits\ServiceContainerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractEventListener implements EventSubscriberInterface {
	use ServiceContainerTrait;

} 